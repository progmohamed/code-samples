<?php

namespace ZekrBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use ZekrBundle\Entity\Video;
use ZekrBundle\Entity\TempVideo;
use ZekrBundle\Form\FtpVideoType;
use ZekrBundle\Form\VideoType;
use ZekrBundle\Classes\FFmpeg;
use AdminBundle\Classes\ChunkedUploadHandler;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * @Route("/video")
 */
class VideoController extends Controller
{
    /**
     * @Route("/", name="zekr_video_list")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('ZekrBundle:Video');
        $dataGrid = $repository->getDataGrid();

        $form = $dataGrid->getFilterForm(
            $this->container->get('form.factory'),
            $this->generateUrl('zekr_video_list')
        );

        if ('POST' == $request->getMethod()) {
            $form->handleRequest($request);
            return $this->redirectToRoute('zekr_video_list', array(
                'filter' => $dataGrid->getEncodedFilterArray($form)
            ));
        }

        $filter = $request->query->get('filter');
        if ($filter) {
            $formData = $dataGrid->decodeFilterArray($filter);
            $form = $dataGrid->setFormFilterData($form, $formData);
        }

        $entities = $dataGrid->getGrid(
            $this->get('knp_paginator'),
            $request->query->getInt('page', 1),
            $this->get('config.service')->getValue('rowsInPage', 10)

        );

        $categories = $em->getRepository("ZekrBundle:Category")->findBy(['deletedAt'=> null, 'active'=>true]);
        $surahs = $em->getRepository("ZekrBundle:Surah")->findAll();
        $hizbs = $em->getRepository("ZekrBundle:Hizb")->findAll();
        $juzs = $em->getRepository("ZekrBundle:Juz")->findAll();
        $videoTypes = $em->getRepository("ZekrBundle:VideoType")->findBy(['deletedAt'=> null, 'active'=>true]);

        return $this->render('ZekrBundle:Video:index.html.twig', array(
            'entities' => $entities,
            'formFilter' => $form->createView(),
            'categories' => $categories,
            'surahs' => $surahs,
            'hizbs' => $hizbs,
            'juzs' => $juzs,
            'videoTypes' => $videoTypes,
        ));
    }


    /**
     * @Route("/new", name="zekr_video_new")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $languages = $this->get('admin.admin_helper')->getLanguages();
        $entity = new Video();
        $form = $this->createForm(VideoType::class, $entity, ['languages' => $languages]);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $tempVideo = $em->getRepository('ZekrBundle:TempVideo')->find($form->get('tempVideo')->getData());
            if ($form->isValid()) {

                $em->getRepository('ZekrBundle:Video')->add(
                    $entity,
                    $tempVideo,
                    $languages,
                    $form,
                    $this->get('search.service')->getIndexer(),
                    $this->container->getParameter('zekr.ffmpeg_binary')
                );

                $taskmanager = $this->get('taskmanager.service');
                $taskmanager->addTaskQueued('zekr:convert-video', ['id' => $entity->getId()], 'convert video');
                //$taskmanager->addTaskRunImmediately('zekr:remove-temp-video', [], 'Removing temp-video');

                $this->get('session')->getFlashBag()->add('success', 'تم حفظ البيانات');

                return $this->redirectToRoute('zekr_video_show', array('id' => $entity->getId()));
            }
        } else {
            foreach ($languages as $language) {
                $form->get('display_' . $language->getLocale())->setData(false);
            }
            $form->get('conversion')->setData((bool)$this->get('config.service')->getValue('convertVideo', 1));
        }

        return $this->render('ZekrBundle:Video:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
            'tempVideo' => (!empty($tempVideo)) ? $tempVideo : null,
        ));
    }


    /**
     * @Route("/upload", name="zekr_video_upload")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function uploadAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        ob_start();
        $uploadDir = realpath($this->get('kernel')->getRootDir() . '/../web/upload/video') . '/';

        $errors = null;
        if (is_array($errors)) {
            $json = json_encode($errors);
        } else {
            $uploadHandlerOptions = array('upload_dir' => $uploadDir);
            $uploadHandler = new ChunkedUploadHandler($uploadHandlerOptions);
            $json = ob_get_contents();

            $existTemp = $em->getRepository("ZekrBundle:TempVideo")->findOneBy(['uuid' => $uploadHandler->getUniqueFileName()]);
            if ($existTemp) {
                if ($uploadHandler->isLastChunk()) {

                    $uploadedFile = $request->files->get('files');

                    $ext = strtolower($uploadedFile->getClientOriginalExtension());
                    $fileName = sha1(uniqid()) . '.' . $ext;
                    rename($uploadDir . $uploadHandler->getUniqueFileName(), $uploadDir . $fileName);
                    $existTemp->setStatus(2);
                    $existTemp->setFile($fileName);
                    $existTemp->setUuid(null);

                    $json = $existTemp->getId();
                    if ($existTemp->getFileAbsolutePath() && file_exists($existTemp->getFileAbsolutePath())) {
                        $ffmpeg = new FFmpeg($existTemp->getFileAbsolutePath());
                        $ffmpeg->setFFmpegBin($this->container->getParameter('zekr.ffmpeg_binary'));
                        if ($ffmpeg->isValid()) {
                            $existTemp->setOriginalName(preg_replace('/\\.[^.\\s]{3,4}$/', '', $uploadedFile->getClientOriginalName()));
                            $existTemp->setDuration($ffmpeg->getDuration());
                        }
                    }
                }
            } else {
                $entity = new TempVideo();
                $entity->setUuid($uploadHandler->getUniqueFileName());
                $entity->setStatus(1);
                $entity->setFile($uploadHandler->getUniqueFileName());

                $em->persist($entity);
            }

            $em->flush();
        }
        ob_end_clean();

        $response = new Response();
        $response->setContent($json);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }


    /**
     * @Route("/ftp_video", name="zekr_video_ftp_video")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Method({"GET", "POST"})
     */
    public function ftpVideoAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $ftpVideoDir = realpath($this->get('kernel')->getRootDir() . '/../web/upload/ftp_video') . '/';
        $videoDir = realpath($this->get('kernel')->getRootDir() . '/../web/upload/video') . '/';
        $uploadThumbnailDir = realpath($this->get('kernel')->getRootDir() . '/../web/upload/video/video_thumbnail') . '/';
        $finder = new Finder();
        $fs = new Filesystem();

        $entity = new Video();
        $finder->files()->in($ftpVideoDir);

        $files  = [];
        foreach ($finder as $file){
            $files[$file->getFilename()] = $file->getFilename();
        }
        $languages = $this->get('admin.admin_helper')->getLanguages();
        $form = $this->createForm(FtpVideoType::class, $entity, ['languages' => $languages, 'files'=>$files]);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $videos = $form->get('videos')->getData();

            foreach ($videos as $video){
                $entity2 = clone $entity;
                $title = preg_replace('/\\.[^.\\s]{3,4}$/', '', $video);
                $videoInfo['title'] = $title;
                $entity2->setPlainSlug($title);
                $ext = pathinfo($video, PATHINFO_EXTENSION);
                $fileName = sha1(uniqid()) . '.' . $ext;
                $videoInfo['file'] = $fileName;
                $fs->rename($ftpVideoDir.$video, $videoDir.$fileName);

                if ($videoDir.$fileName && file_exists($videoDir.$fileName)) {
                    $ffmpeg = new FFmpeg($videoDir.$fileName);
                    $ffmpeg->setFFmpegBin($this->container->getParameter('zekr.ffmpeg_binary'));
                    if ($ffmpeg->isValid()) {
                        $thumbmnailFilename = sha1(uniqid(mt_rand(), true)) . '.jpg';
                        $thumbnailFullPath = $uploadThumbnailDir . $thumbmnailFilename;
                        $ffmpeg->getThumbnail($thumbnailFullPath);


                        $videoInfo['thumbnail'] = $thumbmnailFilename;
                        $videoInfo['duration'] = (int) $ffmpeg->getDuration();
                    }
                }

                $em->getRepository('ZekrBundle:Video')->addFtpVideo(
                    $entity2,
                    $videoInfo,
                    $languages,
                    $form,
                    $this->get('search.service')->getIndexer()
                );

                $taskmanager = $this->get('taskmanager.service');
                $taskmanager->addTaskQueued('zekr:convert-video', ['id' => $entity2->getId()], 'convert video');
            }
            $this->get('session')->getFlashBag()->add('success', 'تم حفظ البيانات');
            return $this->redirectToRoute('zekr_video_ftp_video');
        } else {
            foreach ($languages as $language) {
                $form->get('display_' . $language->getLocale())->setData(true);
            }
        }


        return $this->render('ZekrBundle:Video:ftp_video.html.twig', array(
            'form' => $form->createView(),
        ));

    }


    /**
     * @ParamConverter("entity", class="ZekrBundle:Video")
     * @Route("/show/{id}", name="zekr_video_show")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function showAction(Video $entity)
    {
        $em = $this->getDoctrine()->getManager();

        return $this->render('ZekrBundle:Video:show.html.twig', array(
            'entity' => $entity,
        ));
    }


    /**
     * @ParamConverter("entity", class="ZekrBundle:Video")
     * @Route("/edit/{id}", name="zekr_video_edit")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Video $entity)
    {

        $em = $this->getDoctrine()->getManager();
        $languages = $this->get('admin.admin_helper')->getLanguages();
        $videoRepository = $em->getRepository('ZekrBundle:Video');

        $currentEntites = [];
        $currentEntites['currentThumbnailTime'] =  $entity->getThumbnailTime();
        foreach ($entity->getSurah() as $surah) {
            $currentEntites['currentSurah'][] = $surah->getId();
        }

        foreach ($entity->getHizb() as $hizb) {
            $currentEntites['currentHizb'][] = $hizb->getId();
        }

        foreach ($entity->getJuz() as $juz) {
            $currentEntites['currentJuz'][] = $juz->getId();
        }

        foreach ($entity->getPerson() as $person) {
            $currentEntites['currentPerson'][] = $person->getId();
        }

        foreach ($entity->getVideoType() as $videoType) {
            $currentEntites['currentVideoType'][] = $videoType->getId();
        }

        foreach ($videoRepository->getCollectionForVideo($entity->getId()) as $collection) {
            $currentEntites['currentCollection'][] = $collection->getId();
        }

        if ($entity->getRewaya()) {
            $currentEntites['currentRewaya'][] = $entity->getRewaya()->getId();
        }

        $editForm = $this->createForm(VideoType::class, $entity, ['languages' => $languages, 'edit' => true]);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted()) {
            if ($editForm->isValid()) {
                $conversion = ($editForm->get('conversion')->getData() == null) ? false : $editForm->get('conversion')->getData();
                $entity->setConversion($conversion);
                $videoRepository->edit(
                    $entity,
                    $languages,
                    $editForm,
                    $this->get('search.service')->getIndexer(),
                    $currentEntites,
                    $this->getParameter('zekr.ffmpeg_binary')
                );

                $this->get('session')->getFlashBag()->add('success', 'Data has been updated');
                return $this->redirectToRoute('zekr_video_show', array('id' => $entity->getId()));
            }
        } else {
            foreach ($languages as $language) {
                $editForm->get('title_' . $language->getLocale())->setData($entity->translate($language->getLocale(), false)->getTitle());
                $editForm->get('description_' . $language->getLocale())->setData($entity->translate($language->getLocale(), false)->getDescription());
                $editForm->get('display_' . $language->getLocale())->setData($entity->translate($language->getLocale(), false)->getDisplay());
            }
            $previousCollections = $videoRepository->getCollectionForVideo($entity->getId());
            $editForm->get('collection')->setData($previousCollections);
        }

        return $this->render('ZekrBundle:Video:edit.html.twig', array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
        ));
    }


    /**
     * @Route("/delete/{id}", name="zekr_video_delete")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function deleteAction(Request $request, $id)
    {
        try {
            $this->deleteById($id);
        } catch (\Exception $e) {
            $this->get('session')->getFlashBag()->add(
                'danger', $e->getMessage()
            );
        }
        $encodedRedirect = $request->query->get('redirect');
        $redirect = base64_decode($encodedRedirect);
        if (false === $redirect) {
            $redirect = $this->generateUrl('zekr_video_list');
        }
        return $this->redirect($redirect);
    }

    /**
     * @Route("/batch", name="zekr_video_batch")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function batchAction(Request $request)
    {
        $ids = $request->query->get('ids');
        $action = $request->query->get('action');
        if ($ids) {
            $idx = explode(',', $ids);
            $errCount = 0;
            if (is_array($idx)) {
                foreach ($idx as $id) {
                    if ($action == 'delete') {
                        try {
                            $this->deleteById($id);
                        } catch (\Exception $e) {
                            $errCount++;
                            $this->get('session')->getFlashBag()->add(
                                'danger', $e->getMessage()
                            );
                        }
                    }elseif($action == 'activate' || $action == 'deactivate'){
                        foreach($idx as $id) {
                            try {
                                if ('activate' == $action) {
                                    $this->activateVideoById($id, true);
                                } elseif ('deactivate' == $action) {
                                    $this->activateVideoById($id, false);
                                }
                            }catch(\Exception $e) {
                                $this->get('session')->getFlashBag()->add(
                                    'danger', $e->getMessage()
                                );
                            }
                        }
                    }
                }
            }
        }
        return $this->redirect($this->generateUrl('zekr_video_list'));
    }


    /**
     * @Route("/inline_edit", name="zekr_video_inline_edit")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Method({"GET", "POST"})
     */
    public function inlineEdit(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $languages = $this->get('admin.admin_helper')->getLanguages();
        $entity = $em->getRepository('ZekrBundle:Video')->find($request->request->get('pk'));

        if (!$entity) {
            return new JsonResponse($this->createNotFoundException('غير قادر على إيجاد العنصر'));
        } else {

            $type = $request->request->get('name');
            $value = ($request->request->get('value')) ?: [];
            $indexer = $this->get('search.service')->getIndexer();

            if ($type == 'title') {
                try {
                    foreach ($languages as $language) {
                        $entity->translate($language->getLocale(), false)->setTitle($value['title_' . $language->getLocale()]);
                    }
                    $entity->mergeNewTranslations();
                    $em->flush();
                    $indexer->index($entity);
                } catch (\Exception $e) {
                    return new JsonResponse($e->getMessage());
                }
            }

            if ($type == 'category') {
                try {
                    $em->getRepository('ZekrBundle:Video')->saveVideoCategories($entity, $value, $indexer);
                } catch (\Exception $e) {
                    return new JsonResponse($e->getMessage());
                }
            }

            if ($type == 'video_type') {
                try {
                    $em->getRepository('ZekrBundle:Video')->saveVideoVideoTypes($entity, $value, $indexer);
                } catch (\Exception $e) {
                    return new JsonResponse($e->getMessage());
                }
            }

            if ($type == 'classifications') {
                try {
                    $value['surah'] = (!empty($value['surah'])) ? $value['surah'] : [];
                    $value['hizb'] = (!empty($value['hizb'])) ? $value['hizb'] : [];
                    $value['juz'] = (!empty($value['juz'])) ? $value['juz'] : [];
                    return new JsonResponse($em->getRepository('ZekrBundle:Video')->saveVideoClassifications($entity, $value, $indexer));
                } catch (\Exception $e) {
                    return new JsonResponse($e->getMessage());
                }
            }

        }
        return new JsonResponse($request->request->all());
    }


    private function activateVideoById($id, bool $status)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var Video $entity */
        $entity = $this->getEntityById($id);
        $entity->setActive($status);
        $em->flush();
    }


    private function deleteById($id)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $entity = $this->getEntityById($id);

            $em->getRepository('ZekrBundle:Video')->delete(
                $entity,
                $this->get('search.service')->getIndexer()
            );

            $taskmanager = $this->get('taskmanager.service');
            $taskmanager->addTaskQueued(
                'zekr:remove-video',
                ['id' => $entity->getId()],
                'Removing Video'
            );
        } catch (\Exception $e) {
            throw new \Exception('غير قادر على حذف العنصر "' . $entity . '" ربما يكون مستخدماً"');
        }
    }


    private function getEntityById($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ZekrBundle:Video')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('غير قادر على إيجاد العنصر');
        }
        return $entity;
    }

}
