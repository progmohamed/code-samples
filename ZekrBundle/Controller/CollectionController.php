<?php

namespace ZekrBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use ZekrBundle\Entity\Collection;
use ZekrBundle\Form\CollectionType;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/collection")
 */
class CollectionController extends Controller
{
    /**
     * @Route("/", name="zekr_collection_list")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('ZekrBundle:Collection');
        $dataGrid = $repository->getDataGrid();

        $form = $dataGrid->getFilterForm(
            $this->container->get('form.factory'),
            $this->generateUrl('zekr_collection_list')
        );

        if ('POST' == $request->getMethod()) {
            $form->handleRequest($request);
            return $this->redirectToRoute('zekr_collection_list', array(
                'filter' => $dataGrid->getEncodedFilterArray($form)
            ));
        }

        $filter = $request->query->get('filter');
        if ($filter) {
            $formData = $dataGrid->decodeFilterArray($filter);
            $form = $dataGrid->setFormFilterData($form, $formData);
        }

        $entities = $dataGrid->getGrid();

        return $this->render('ZekrBundle:Collection:index.html.twig', array(
            'entities' => $entities,
            'formFilter' => $form->createView(),
        ));
    }


    /**
     * @Route("/new", name="zekr_collection_new")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $languages = $this->get('admin.admin_helper')->getLanguages();
        $collectionRepository = $em->getRepository('ZekrBundle:Collection');
        $entity = new Collection();
        $form = $this->createForm(CollectionType::class, $entity, ['languages' => $languages]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                foreach ($languages as $language) {
                    $entity->translate($language->getLocale())->setName($form->get('name_' . $language->getLocale())->getData());
                    $entity->translate($language->getLocale())->setDisplay($form->get('display_' . $language->getLocale())->getData());
                }
                $entity->mergeNewTranslations();

                $entity->setSortOrder($collectionRepository->getMaxOrder() + 1);
                $em->persist($entity);
                $em->flush();

                $this->get('session')->getFlashBag()->add(
                    'success', 'تم حفظ البيانات'
                );
                return $this->redirectToRoute('zekr_collection_show', array('id' => $entity->getId()));
            }
        } else {
            foreach ($languages as $language) {
                $form->get('display_' . $language->getLocale())->setData(true);
            }
        }

        return $this->render('ZekrBundle:Collection:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }


    /**
     * @ParamConverter("entity", class="ZekrBundle:Collection")
     * @Route("/show/{id}", name="zekr_collection_show")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function showAction(Collection $entity)
    {
        $em = $this->getDoctrine()->getManager();
        return $this->render('ZekrBundle:Collection:show.html.twig', array(
            'entity' => $entity,
        ));
    }


    /**
     * @ParamConverter("entity", class="ZekrBundle:Collection")
     * @Route("/edit/{id}", name="zekr_collection_edit")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Collection $entity)
    {

        $em = $this->getDoctrine()->getManager();
        $languages = $this->get('admin.admin_helper')->getLanguages();
        $editForm = $this->createForm(CollectionType::class, $entity, ['languages'=>$languages] );
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted()) {
            if($editForm->isValid()) {
                foreach($languages as $language) {
                    $entity->translate($language->getLocale(), false)->setName($editForm->get('name_'.$language->getLocale())->getData());
                    $entity->translate($language->getLocale(), false)->setDisplay($editForm->get('display_' . $language->getLocale())->getData());
                }
                $entity->mergeNewTranslations();
                $em->flush();
                $this->get('session')->getFlashBag()->add(
                    'success', 'Data has been updated'
                );
                return $this->redirectToRoute('zekr_collection_show', array('id' => $entity->getId()));
            }
        }else{
            foreach($languages as $language) {
                $editForm->get('name_'.$language->getLocale())->setData($entity->translate($language->getLocale(), false)->getName());
                $editForm->get('display_' . $language->getLocale())->setData($entity->translate($language->getLocale(), false)->getDisplay());
            }
        }

        return $this->render('ZekrBundle:Collection:edit.html.twig', array(
            'entity'        => $entity,
            'edit_form'     => $editForm->createView(),
        ));
    }



    /**
     * @ParamConverter("entity", class="ZekrBundle:Collection")
     * @Route("/resort_video/{id}", name="zekr_collection_resort_video")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Method({"GET", "POST"})
     */
    public function resortVideoAction(Request $request, Collection $entity)
    {

        $em = $this->getDoctrine()->getManager();
        $videosForCollection = $em->getRepository("ZekrBundle:Collection")->getVideoForCollection($entity);

        return $this->render('ZekrBundle:Collection:resort.html.twig', array(
            'entity'        => $entity,
            'videos_for_collection'  =>$videosForCollection,
        ));
    }


    /**
     * @ParamConverter("entity", class="ZekrBundle:Collection")
     * @Route("/resort_video_ajax/{id}", name="zekr_collection_resort_video_ajax")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function resortVideoAjaxAction(Request $request, Collection $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $collectionRepository = $em->getRepository('ZekrBundle:Collection');
        $collectionRepository->resortVideo($request->request->get('sortArr'));

        $taskmanager = $this->get('taskmanager.service');
        $taskmanager->addTaskRunImmediately('zekr:index-video-collection', ['collection' => $entity->getId()], 'Resort video Collection');

        return  new JsonResponse($request->request->get('sortArr'));
    }

    /**
     * @Route("/resort", name="zekr_collection_resort")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function resortAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $collectionRepository = $em->getRepository('ZekrBundle:Collection');
        $collectionRepository->resort($request->request->get('sortArr'));

        return  new JsonResponse($request->request->get('sortArr'));
    }



    /**
     * @Route("/delete/{id}", name="zekr_collection_delete")
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
        $redirect = base64_decode( $encodedRedirect );
        if(false === $redirect) {
            $redirect = $this->generateUrl('zekr_collection_list');
        }
        return $this->redirect( $redirect );
    }

    /**
     * @Route("/batch", name="zekr_collection_batch")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function batchAction(Request $request)
    {
        $ids = $request->query->get('ids');
        $action = $request->query->get('action');
        if($ids) {
            $idx = explode(',', $ids);
            $errCount = 0;
            if(is_array($idx)) {
                foreach($idx as $id) {
                    if($action == 'delete') {
                        try {
                            $this->deleteById($id);
                        }catch(\Exception $e) {
                            $errCount ++;
                            $this->get('session')->getFlashBag()->add(
                                'danger', $e->getMessage()
                            );
                        }
                    }
                }
            }
        }
        return $this->redirect($this->generateUrl('zekr_collection_list'));
    }

    private function deleteById($id)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $entity = $this->getEntityById($id);
            //softDelete

            $randomValue = sha1(uniqid(time(), true));
            $entity->setPlainSlug( $randomValue);

            $em->remove($entity);
            $em->flush();

            $taskmanager = $this->get('taskmanager.service');
            $taskmanager->addTaskQueued(
                'zekr:remove-collection',
                [ 'id' => $entity->getId() ],
                'Removing collection'
            );
        } catch (\Exception $e) {
            throw new \Exception('غير قادر على حذف العنصر "'.$entity.'" ربما يكون مستخدماً"');
        }
    }

    private function getEntityById($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ZekrBundle:Collection')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('غير قادر على إيجاد العنصر');
        }
        return $entity;
    }

}
