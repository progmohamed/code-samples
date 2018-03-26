<?php

namespace ZekrBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use ZekrBundle\Entity\Rewaya;
use ZekrBundle\Form\RewayaType;
use Symfony\Component\HttpFoundation\JsonResponse;


/**
 * @Route("/rewaya")
 */
class RewayaController extends Controller
{
    /**
     * @Route("/", name="zekr_rewaya_list")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('ZekrBundle:Rewaya');
        $dataGrid = $repository->getDataGrid();

        $form = $dataGrid->getFilterForm(
            $this->container->get('form.factory'),
            $this->generateUrl('zekr_rewaya_list')
        );

        if ('POST' == $request->getMethod()) {
            $form->handleRequest($request);
            return $this->redirectToRoute('zekr_rewaya_list', array(
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

        return $this->render('ZekrBundle:Rewaya:index.html.twig', array(
            'entities' => $entities,
            'formFilter' => $form->createView(),
        ));
    }


    /**
     * @Route("/new", name="zekr_rewaya_new")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $languages = $this->get('admin.admin_helper')->getLanguages();
        $rewayaRepository = $em->getRepository('ZekrBundle:Rewaya');
        $entity = new Rewaya();
        $form = $this->createForm(RewayaType::class, $entity, ['languages'=> $languages]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            foreach($languages as $language) {
                $entity->translate($language->getLocale())->setName($form->get('name_'.$language->getLocale())->getData());
            }
            $entity->mergeNewTranslations();
            $entity->setSortOrder($rewayaRepository->getMaxOrder() + 1);
            $em->persist($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success','تم حفظ البيانات'
            );
            return $this->redirectToRoute('zekr_rewaya_show', array('id'=>$entity->getId()));
        }

        return $this->render('ZekrBundle:Rewaya:new.html.twig', array(
            'entity'    => $entity,
            'form'      => $form->createView(),
        ));
    }


    /**
     * @ParamConverter("entity", class="ZekrBundle:Rewaya")
     * @Route("/show/{id}", name="zekr_rewaya_show")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function showAction(Rewaya $entity)
    {
        $em = $this->getDoctrine()->getManager();
        return $this->render('ZekrBundle:Rewaya:show.html.twig', array(
            'entity' => $entity,
        ));
    }


    /**
     * @ParamConverter("entity", class="ZekrBundle:Rewaya")
     * @Route("/edit/{id}", name="zekr_rewaya_edit")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Rewaya $entity)
    {

        $em = $this->getDoctrine()->getManager();
        $languages = $this->get('admin.admin_helper')->getLanguages();
        $editForm = $this->createForm(RewayaType::class, $entity, ['languages'=>$languages] );
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted()) {
            if($editForm->isValid()) {
                foreach($languages as $language) {
                    $entity->translate($language->getLocale(), false)->setName($editForm->get('name_'.$language->getLocale())->getData());
                }
                $entity->mergeNewTranslations();
                $em->flush();
                $this->get('session')->getFlashBag()->add(
                    'success', 'Data has been updated'
                );
                return $this->redirectToRoute('zekr_rewaya_show', array('id' => $entity->getId()));
            }
        }else{
            foreach($languages as $language) {
                $editForm->get('name_'.$language->getLocale())->setData($entity->translate($language->getLocale(), false)->getName());
            }
        }


        return $this->render('ZekrBundle:Rewaya:edit.html.twig', array(
            'entity'        => $entity,
            'edit_form'     => $editForm->createView(),
        ));
    }



    /**
     * @Route("/resort", name="zekr_rewaya_resort")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function resortAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $rewayaRepository = $em->getRepository('ZekrBundle:Rewaya');
        $rewayaRepository->resort($request->request->get('sortArr'));

        return  new JsonResponse($request->request->get('sortArr'));
    }


    /**
     * @Route("/delete/{id}", name="zekr_rewaya_delete")
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
            $redirect = $this->generateUrl('zekr_rewaya_list');
        }
        return $this->redirect( $redirect );
    }

    /**
     * @Route("/batch", name="zekr_rewaya_batch")
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
        return $this->redirect($this->generateUrl('zekr_rewaya_list'));
    }

    private function deleteById($id)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $entity = $this->getEntityById($id);
            //SoftDelete

            $randomValue = sha1(uniqid(time(), true));
            $entity->setPlainSlug( $randomValue);

            $em->remove($entity);
            $em->flush();

            $taskmanager = $this->get('taskmanager.service');
            $taskmanager->addTaskQueued(
                'zekr:remove-rewaya',
                [ 'id' => $entity->getId() ],
                'Removing Rewaya'
            );

        } catch (\Exception $e) {
            throw new \Exception('غير قادر على حذف العنصر "'.$entity.'" ربما يكون مستخدماً"');
        }
    }

    private function getEntityById($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ZekrBundle:Rewaya')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('غير قادر على إيجاد العنصر');
        }
        return $entity;
    }

}
