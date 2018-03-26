<?php

namespace TaskManagerBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/task-viewer")
 */
class TaskViewerController extends Controller
{
    /**
     * @Route("/", name="taskmanager_taskviewer_list")
     * @Security("has_role('ROLE_TASKMANAGER_TASKVIEWER_LIST')")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $userRepository = $em->getRepository('TaskManagerBundle:Task');
        $dataGrid = $userRepository->getDataGrid();
        $form = $dataGrid->getFilterForm(
            $this->container->get('form.factory'),
            $this->generateUrl('taskmanager_taskviewer_list')
        );

        if('POST' == $request->getMethod()) {
            $form->handleRequest($request);
            return $this->redirectToRoute('taskmanager_taskviewer_list', [
                'filter' => $dataGrid->getEncodedFilterArray($form)
            ]);
        }

        $filter = $request->query->get('filter');
        if($filter) {
            $formData = $dataGrid->decodeFilterArray($filter);
            $form = $dataGrid->setFormFilterData($form, $formData);
        }

        $entities = $dataGrid->getGrid(
            $this->get('knp_paginator'),
            $request->query->getInt('page', 1)
        );

        return $this->render('TaskManagerBundle:TaskViewer:index.html.twig', [
            'entities' => $entities,
            'formFilter' => $form->createView(),
        ]);
    }
}
