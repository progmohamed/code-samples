<?php

namespace ZekrBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use ZekrBundle\Entity\StaticPage;
use ZekrBundle\Form\StaticPageType;

/**
 * @Route("/static_page")
 */
class StaticPageController extends Controller
{
    /**
     * @Route("/", name="zekr_static_page_list")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('ZekrBundle:StaticPage');
        $dataGrid = $repository->getDataGrid();

        $formData = array();
        $entities = $dataGrid->getGrid(
            $this->get('knp_paginator'),
            $request->query->getInt('page', 1),
            $this->get('config.service')->getValue('rowsInPage', 10)
        );

        return $this->render('ZekrBundle:StaticPage:index.html.twig', array(
            'entities' => $entities,
        ));

    }


    /**
     * @ParamConverter("entity", class="ZekrBundle:StaticPage")
     * @Route("/show/{id}", name="zekr_static_page_show")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function showAction(StaticPage $entity)
    {
        $em = $this->getDoctrine()->getManager();
        return $this->render('ZekrBundle:StaticPage:show.html.twig', array(
            'entity' => $entity,
        ));
    }


    /**
     * @ParamConverter("entity", class="ZekrBundle:StaticPage")
     * @Route("/edit/{id}", name="zekr_static_page_edit")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, StaticPage $entity)
    {

        $em = $this->getDoctrine()->getManager();
        $languages = $this->get('admin.admin_helper')->getLanguages();
        $editForm = $this->createForm(StaticPageType::class, $entity, ['languages'=>$languages]);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted()) {
            if($editForm->isValid()) {
                foreach($languages as $language) {
                    $entity->translate($language->getLocale(), false)->setContent($editForm->get('content_'.$language->getLocale())->getData());
                    $entity->translate($language->getLocale(), false)->setTitle($editForm->get('title_'.$language->getLocale())->getData());
                }
                $entity->mergeNewTranslations();
                $em->flush();
                $this->get('session')->getFlashBag()->add(
                    'success', 'Data has been updated'
                );
                return $this->redirectToRoute('zekr_static_page_show', array('id' => $entity->getId()));
            }
        }else{
            foreach($languages as $language) {
                $editForm->get('content_'.$language->getLocale())->setData($entity->translate($language->getLocale(), false)->getContent());
                $editForm->get('title_'.$language->getLocale())->setData($entity->translate($language->getLocale(), false)->getTitle());
            }
        }

        return $this->render('ZekrBundle:StaticPage:edit.html.twig', array(
            'entity'        => $entity,
            'edit_form'     => $editForm->createView(),
        ));
    }


    /**
     * @Route("/delete/{id}", name="zekr_static_page_delete")
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
            $redirect = $this->generateUrl('zekr_static_page_list');
        }
        return $this->redirect( $redirect );
    }

    /**
     * @Route("/batch", name="zekr_static_page_batch")
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
        return $this->redirect($this->generateUrl('zekr_static_page_list'));
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
        $entity = $em->getRepository('ZekrBundle:StaticPage')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('غير قادر على إيجاد العنصر');
        }
        return $entity;
    }

}
