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
use ZekrBundle\Entity\SelectedCollection;

/**
 * @Route("/selected_collection")
 */
class SelectedCollectionController extends Controller
{

    /**
     * @Route("/", name="zekr_selected_collection_list")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $formData = array();
        $SelectedCollectionRepository = $em->getRepository('ZekrBundle:SelectedCollection');

        $entities = $SelectedCollectionRepository->getGrid(
            $request->query->getInt('page', 1),
            $this->get('knp_paginator'),
            $formData
        );
        return $this->render('ZekrBundle:SelectedCollection:list.html.twig', array(
            'entities' => $entities
        ));
    }

    /**
     * @Route("/delete/{id}", name="zekr_selected_collection_delete")
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
            $redirect = $this->generateUrl('zekr_selected_collection_list');
        }
        return $this->redirect($redirect);
    }

    private function deleteById($id)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $SelectedCollection = $this->getEntityById($id);
            $collection = $SelectedCollection->getCollection();
            $em->remove($SelectedCollection);
            $collection->setSelected(false);
            $em->flush();
        } catch (\Exception $e) {
            throw new \Exception('غير قادر على حذف العنصر "' . $SelectedCollection . '" ربما يكون مستخدماً"');
        }
    }

    private function getEntityById($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ZekrBundle:SelectedCollection')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('غير قادر على إيجاد العنصر');
        }
        return $entity;
    }

    /**
     * @Route("/add-remove-selected-collection", name="zekr_add_remove_selected_collection")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function addRemoveSelectedCollectionAction(Request $request)
    {
        $ajaxResponse = new AjaxResponse();
        try {
            $collectionId = $request->request->get('collection');
            $mode = $request->request->get('mode');
            $em = $this->getDoctrine()->getManager();
            $collectionRepository = $em->getRepository('ZekrBundle:Collection');
            $collection = $collectionRepository->find($collectionId);
            if (!$collection) {
                throw new \Exception('المجموعة غير موجودة');
            }
            $selectedCollectionRepository = $em->getRepository('ZekrBundle:SelectedCollection');
            $selectedCollection = $selectedCollectionRepository->findOneBy(array('collection' => $collection));
            if ('add' == $mode) {
                $collection->setSelected(true);
                if (!$selectedCollection) {
                    $selectedCollection = new SelectedCollection();
                    $selectedCollection->setCollection($collection);
                    $selectedCollection->setSortOrder($selectedCollectionRepository->getMaxOrder() + 1);
                    $em->persist($selectedCollection);
                    $em->flush();
                }
            } else if ('remove' == $mode) {
                $collection->setSelected(false);
                if ($selectedCollection) {
                    $em->remove($selectedCollection);
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
     * @Route("/batch", name="zekr_selected_collection_batch")
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
        return $this->redirect($this->generateUrl('zekr_selected_collection_list'));
    }

    /**
     * @Route("/resort_collection", name="zekr_selected_collection_resort")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function resortCollectionAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('ZekrBundle:SelectedCollection');
        $repository->resort($request->request->get('sortArr'));

        return  new JsonResponse($request->request->get('sortArr'));
    }

    /**
     * @ParamConverter("entity", class="ZekrBundle:SelectedCollection")
     * @Route("/move-up/{entity}", name="zekr_selected_collection_moveup")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function moveUpAction(Request $request, SelectedCollection $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $photoRepository = $em->getRepository('ZekrBundle:SelectedCollection');
        $photoRepository->moveUp($entity);
        $encodedRedirect = $request->query->get('redirect');
        $redirect = base64_decode( $encodedRedirect );
        return $this->redirect($this->generateUrl('zekr_selected_collection_list', array()));
    }

    /**
     * @ParamConverter("entity", class="ZekrBundle:SelectedCollection")
     * @Route("/move-down/{entity}", name="zekr_selected_collection_movedown")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function moveDownAction(Request $request, SelectedCollection $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $photoRepository = $em->getRepository('ZekrBundle:SelectedCollection');
        $photoRepository->moveDown($entity);
        $encodedRedirect = $request->query->get('redirect');
        $redirect = base64_decode( $encodedRedirect );
        return $this->redirect($this->generateUrl('zekr_selected_collection_list', array()));
    }
}
