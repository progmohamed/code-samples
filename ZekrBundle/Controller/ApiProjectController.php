<?php

namespace ZekrBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use ZekrBundle\Entity\ApiProject;
use ZekrBundle\Form\ApiProjectType;


/**
 * @Route("/api_project")
 */
class ApiProjectController extends Controller
{
    /**
     * @Route("/", name="zekr_api_project_list")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('ZekrBundle:ApiProject');
        $dataGrid = $repository->getDataGrid();

        $form = $dataGrid->getFilterForm(
            $this->container->get('form.factory'),
            $this->generateUrl('zekr_api_project_list')
        );

        if ('POST' == $request->getMethod()) {
            $form->handleRequest($request);
            return $this->redirectToRoute('zekr_api_project_list', array(
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

        return $this->render('ZekrBundle:ApiProject:index.html.twig', array(
            'entities' => $entities,
            'formFilter' => $form->createView(),
        ));
    }


    /**
     * @Route("/new", name="zekr_api_project_new")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $apiProjectRepository = $em->getRepository('ZekrBundle:ApiProject');
        $entity = new ApiProject();
        $form = $this->createForm(ApiProjectType::class, $entity, []);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success','تم حفظ البيانات'
            );
            return $this->redirectToRoute('zekr_api_project_show', array('id'=>$entity->getId()));
        }

        return $this->render('ZekrBundle:ApiProject:new.html.twig', array(
            'entity'    => $entity,
            'form'      => $form->createView(),
        ));
    }


    /**
     * @ParamConverter("entity", class="ZekrBundle:ApiProject")
     * @Route("/show/{id}", name="zekr_api_project_show")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function showAction(ApiProject $entity)
    {
        $em = $this->getDoctrine()->getManager();
        return $this->render('ZekrBundle:ApiProject:show.html.twig', array(
            'entity' => $entity,
        ));
    }


    /**
     * @ParamConverter("entity", class="ZekrBundle:ApiProject")
     * @Route("/edit/{id}", name="zekr_api_project_edit")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, ApiProject $entity)
    {

        $em = $this->getDoctrine()->getManager();
        $editForm = $this->createForm(ApiProjectType::class, $entity, [] );
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted()) {
            if($editForm->isValid()) {
                $em->flush();
                $this->get('session')->getFlashBag()->add(
                    'success', 'Data has been updated'
                );
                return $this->redirectToRoute('zekr_api_project_show', array('id' => $entity->getId()));
            }
        }

        return $this->render('ZekrBundle:ApiProject:edit.html.twig', array(
            'entity'        => $entity,
            'edit_form'     => $editForm->createView(),
        ));
    }


    /**
     * @Route("/delete/{id}", name="zekr_api_project_delete")
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
            $redirect = $this->generateUrl('zekr_api_project_list');
        }
        return $this->redirect( $redirect );
    }

    /**
     * @Route("/batch", name="zekr_api_project_batch")
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
        return $this->redirect($this->generateUrl('zekr_api_project_list'));
    }

    private function deleteById($id)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $entity = $this->getEntityById($id);
            $em->remove($entity);
            $em->flush();
        } catch (\Exception $e) {
            throw new \Exception('غير قادر على حذف العنصر "'.$entity.'" ربما يكون مستخدماً"');
        }
    }

    private function getEntityById($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ZekrBundle:ApiProject')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('غير قادر على إيجاد العنصر');
        }
        return $entity;
    }

}
