<?php

namespace SearchBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/history")
 */
class HistoryController extends Controller
{
    /**
     * @Route("/", name="search_history_list")
     * @Security("has_role('ROLE_SEARCH_HISTORY_LIST')")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('SearchBundle:History');
        $dataGrid = $repository->getDataGrid();

        $form = $dataGrid->getFilterForm(
            $this->container->get('form.factory'),
            $this->generateUrl('search_history_list')
        );

        if ('POST' == $request->getMethod()) {
            $form->handleRequest($request);
            return $this->redirectToRoute('search_history_list', [
                'filter' => $dataGrid->getEncodedFilterArray($form)
            ]);
        }

        $filter = $request->query->get('filter');
        if ($filter) {
            $formData = $dataGrid->decodeFilterArray($filter);
            $form = $dataGrid->setFormFilterData($form, $formData);
        }

        $entities = $dataGrid->getGrid(
            $this->get('knp_paginator'),
            $request->query->getInt('page', 1)
        );

        return $this->render('SearchBundle:History:index.html.twig', [
            'entities' => $entities,
            'formFilter' => $form->createView(),
        ]);
    }


    /**
     * @Route("/delete", name="search_history_delete")
     * @Security("has_role('ROLE_SEARCH_HISTORY_DELETE')")
     */
    public function deleteAction(Request $request)
    {
        $em = $this->get('doctrine')->getManager();
        $single = $request->query->get('single', false);
        $id = $request->query->get('id');
        $encodedRedirect = $request->query->get('redirect');
        $redirect = base64_decode( $encodedRedirect );
        if ($single) {
            return $this->redirectToRoute('search_history_delete', [
                'id' => base64_encode(serialize([$id])),
                'redirect' => $encodedRedirect
            ]);
        } else {
            $repository = $em->getRepository('SearchBundle:History');
            $bundleService = $this->get('history.service');
            $ids = unserialize(base64_decode($id));
            if (!is_array($ids) || empty($ids)) {
                throw $this->createNotFoundException( $this->get('translator')->trans('admin.titles.error_happened') );
            }
            if ('POST' == $request->getMethod()) {
                foreach($ids as $id) {
                    try {
                        $entity = $repository->find($id);
                        if($entity) {
                            $em->remove($entity);
                            $em->flush();
                            $this->get('session')->getFlashBag()->add(
                                'success',  $this->get('translator')->trans('admin.messages.the_entry_has_been_deleted').' '. $entity
                            );
                        }
                    }catch(\Exception $e) {
                        $this->get('session')->getFlashBag()->add(
                            'danger', $e->getMessage()
                        );
                    }
                }
                return $this->redirectToRoute('search_history_list');
            }else{
                $report = $repository->getDeleteRestrectionsByIds(
                    $bundleService,
                    $ids
                );
                return $this->render('SearchBundle:History:delete.html.twig', [
                    'report' => $report,
                    'redirect' => $redirect,
                ]);
            }
        }
    }


    /**
     * @Route("/batch", name="search_history_batch")
     * @Security("has_role('ROLE_SEARCH_HISTORY_DELETE')")
     */
    public function batchAction(Request $request)
    {
        $ids = $request->query->get('ids');
        $idx = explode(',', $ids);
        $action = $request->query->get('action');
        if ('delete' == $action) {
            return $this->redirectToRoute('search_history_delete', [
                'id' => base64_encode(serialize($idx))
            ]);
        }
    }

}
