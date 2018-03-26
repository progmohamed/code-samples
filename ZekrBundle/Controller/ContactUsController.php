<?php

namespace ZekrBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use ZekrBundle\Entity\ContactUs;

/**
 * @Route("/contact_us")
 */
class ContactUsController extends Controller
{
    /**
     * @Route("/", name="zekr_contact_us_list")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('ZekrBundle:ContactUs');
        $dataGrid = $repository->getDataGrid();

        $form = $dataGrid->getFilterForm(
            $this->container->get('form.factory'),
            $this->generateUrl('zekr_contact_us_list')
        );

        if ('POST' == $request->getMethod()) {
            $form->handleRequest($request);
            return $this->redirectToRoute('zekr_contact_us_list', array(
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

        return $this->render('ZekrBundle:ContactUs:index.html.twig', array(
            'entities' => $entities,
            'formFilter' => $form->createView(),
        ));
    }



    /**
     * @ParamConverter("entity", class="ZekrBundle:ContactUs")
     * @Route("/show/{id}", name="zekr_contact_us_show")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function showAction(ContactUs $entity)
    {
        $em = $this->getDoctrine()->getManager();
        return $this->render('ZekrBundle:ContactUs:show.html.twig', array(
            'entity' => $entity,
        ));
    }


    /**
     * @Route("/delete/{id}", name="zekr_contact_us_delete")
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
            $redirect = $this->generateUrl('zekr_contact_us_list');
        }
        return $this->redirect( $redirect );
    }

    /**
     * @Route("/batch", name="zekr_contact_us_batch")
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
        return $this->redirect($this->generateUrl('zekr_contact_us_list'));
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
        $entity = $em->getRepository('ZekrBundle:ContactUs')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('غير قادر على إيجاد العنصر');
        }
        return $entity;
    }

}
