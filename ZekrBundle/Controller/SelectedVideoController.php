<?php

namespace ZekrBundle\Controller;

use AdminBundle\Classes\AjaxResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use ZekrBundle\Entity\SelectedVideo;

/**
 * @Route("/selected_video")
 */
class SelectedVideoController extends Controller
{

    /**
     * @Route("/", name="zekr_selected_video_list")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $formData = array();
        $SelectedVideoRepository = $em->getRepository('ZekrBundle:SelectedVideo');

        $entities = $SelectedVideoRepository->getGrid(
            $request->query->getInt('page', 1),
            $this->get('knp_paginator'),
            $formData
        );
        return $this->render('ZekrBundle:SelectedVideo:list.html.twig', array(
            'entities' => $entities
        ));
    }

    /**
     * @Route("/delete/{id}", name="zekr_selected_video_delete")
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
            $redirect = $this->generateUrl('zekr_selected_video_list');
        }
        return $this->redirect($redirect);
    }

    private function deleteById($id)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $SelectedVideo = $this->getEntityById($id);
            $video = $SelectedVideo->getVideo();
            $em->remove($SelectedVideo);
            $video->setSelected(false);
            $em->flush();
        } catch (\Exception $e) {
            throw new \Exception('غير قادر على حذف العنصر "' . $SelectedVideo . '" ربما يكون مستخدماً"');
        }
    }

    private function getEntityById($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ZekrBundle:SelectedVideo')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('غير قادر على إيجاد العنصر');
        }
        return $entity;
    }

    /**
     * @Route("/add-remove-selected-video", name="zekr_add_remove_selected_video")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function addRemoveSelectedVideoAction(Request $request)
    {
        $ajaxResponse = new AjaxResponse();
        try {
            $videoId = $request->request->get('video');
            $mode = $request->request->get('mode');
            $em = $this->getDoctrine()->getManager();
            $videoRepository = $em->getRepository('ZekrBundle:Video');
            $video = $videoRepository->find($videoId);
            if (!$video) {
                throw new \Exception('الفتوى غير موجودة');
            }
            $SelectedVideoRepository = $em->getRepository('ZekrBundle:SelectedVideo');
            $SelectedVideo = $SelectedVideoRepository->findOneBy(array('video' => $video));
            if ('add' == $mode) {
                $video->setSelected(true);
                if (!$SelectedVideo) {
                    $SelectedVideo = new SelectedVideo();
                    $SelectedVideo->setVideo($video);
                    $SelectedVideo->setSortOrder($SelectedVideoRepository->getMaxOrder() + 1);
                    $em->persist($SelectedVideo);
                    $em->flush();
                }
            } else if ('remove' == $mode) {
                $video->setSelected(false);
                if ($SelectedVideo) {
                    $em->remove($SelectedVideo);
                    $em->flush();
                }
            } else {
                throw new \Exception('عملية غير معروفة');
            }
        } catch (\Exception $e) {
            $ajaxResponse->setSuccess(false);
            $ajaxResponse->setMessage($e->getMessage());
        }
        return new JsonResponse($ajaxResponse->getArray());
    }


    /**
     * @Route("/batch", name="zekr_selected_video_batch")
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
                    }
                }
            }
        }
        return $this->redirect($this->generateUrl('zekr_selected_video_list'));
    }

    /**
     * @Route("/resort_videos", name="zekr_selected_video_resort")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function resortVideoAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('ZekrBundle:SelectedVideo');
        $repository->resort($request->request->get('sortArr'));
        return  new JsonResponse($request->request->get('sortArr'));
    }

    /**
     * @ParamConverter("entity", class="ZekrBundle:SelectedVideo")
     * @Route("/move-up/{entity}", name="zekr_selected_video_moveup")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function moveUpAction(Request $request, SelectedVideo $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('ZekrBundle:SelectedVideo');
        $repository->moveUp($entity);
        $encodedRedirect = $request->query->get('redirect');
        $redirect = base64_decode( $encodedRedirect );
        return $this->redirect($this->generateUrl('zekr_selected_video_list', array()));
    }

    /**
     * @ParamConverter("entity", class="ZekrBundle:SelectedVideo")
     * @Route("/move-down/{entity}", name="zekr_selected_video_movedown")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function moveDownAction(Request $request, SelectedVideo $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('ZekrBundle:SelectedVideo');
        $repository->moveDown($entity);
        $encodedRedirect = $request->query->get('redirect');
        $redirect = base64_decode( $encodedRedirect );
        return $this->redirect($this->generateUrl('zekr_selected_video_list', array()));
    }
}
