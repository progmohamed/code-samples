<?php

namespace ZekrBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use ZekrBundle\Entity\Person;
use ZekrBundle\Form\PersonType;

/**
 * @Route("/person")
 */
class PersonController extends Controller
{
    /**
     * @Route("/", name="zekr_person_list")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('ZekrBundle:Person');
        $dataGrid = $repository->getDataGrid();

        $form = $dataGrid->getFilterForm(
            $this->container->get('form.factory'),
            $this->generateUrl('zekr_person_list')
        );

        if ('POST' == $request->getMethod()) {
            $form->handleRequest($request);
            return $this->redirectToRoute('zekr_person_list', array(
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

        return $this->render('ZekrBundle:Person:index.html.twig', array(
            'entities' => $entities,
            'formFilter' => $form->createView(),
        ));
    }


    /**
     * @Route("/new", name="zekr_person_new")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $languages = $this->get('admin.admin_helper')->getLanguages();
        $entity = new Person();
        $form = $this->createForm(PersonType::class, $entity, ['languages'=> $languages]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            foreach($languages as $language) {
                $entity->translate($language->getLocale())->setName($form->get('name_'.$language->getLocale())->getData());
                $entity->translate($language->getLocale())->setResume($form->get('resume_'.$language->getLocale())->getData());
            }
            $entity->mergeNewTranslations();

            $em->persist($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success','تم حفظ البيانات'
            );
            return $this->redirectToRoute('zekr_person_show', array('id'=>$entity->getId()));
        }

        return $this->render('ZekrBundle:Person:new.html.twig', array(
            'entity'    => $entity,
            'form'      => $form->createView(),
        ));
    }


    /**
     * @ParamConverter("entity", class="ZekrBundle:Person")
     * @Route("/show/{id}", name="zekr_person_show")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function showAction(Person $entity)
    {
        $em = $this->getDoctrine()->getManager();
        return $this->render('ZekrBundle:Person:show.html.twig', array(
            'entity' => $entity,
        ));
    }


    /**
     * @ParamConverter("entity", class="ZekrBundle:Person")
     * @Route("/edit/{id}", name="zekr_person_edit")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Person $entity)
    {

        $em = $this->getDoctrine()->getManager();
        $languages = $this->get('admin.admin_helper')->getLanguages();
        $editForm = $this->createForm(PersonType::class, $entity, ['languages'=>$languages, 'edit'=>true] );
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted()) {
            if($editForm->isValid()) {
                foreach($languages as $language) {
                    $entity->translate($language->getLocale(), false)->setName($editForm->get('name_'.$language->getLocale())->getData());
                    $entity->translate($language->getLocale(), false)->setResume($editForm->get('resume_'.$language->getLocale())->getData());
                }
                $entity->mergeNewTranslations();
                $em->flush();
                $this->get('session')->getFlashBag()->add(
                    'success', 'Data has been updated'
                );
                return $this->redirectToRoute('zekr_person_show', array('id' => $entity->getId()));
            }
        }else{
            foreach($languages as $language) {
                $editForm->get('name_'.$language->getLocale())->setData($entity->translate($language->getLocale(), false)->getName());
                $editForm->get('resume_'.$language->getLocale())->setData($entity->translate($language->getLocale(), false)->getResume());
            }
        }

        return $this->render('ZekrBundle:Person:edit.html.twig', array(
            'entity'        => $entity,
            'edit_form'     => $editForm->createView(),
        ));
    }


    /**
     * @Route("/delete/{id}", name="zekr_person_delete")
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
            $redirect = $this->generateUrl('zekr_person_list');
        }
        return $this->redirect( $redirect );
    }

    /**
     * @Route("/batch", name="zekr_person_batch")
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
        return $this->redirect($this->generateUrl('zekr_person_list'));
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
                'zekr:remove-person',
                [ 'id' => $entity->getId() ],
                'Removing Person'
            );

        } catch (\Exception $e) {
            throw new \Exception('غير قادر على حذف العنصر "'.$entity.'" ربما يكون مستخدماً"');
        }
    }

    private function getEntityById($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ZekrBundle:Person')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('غير قادر على إيجاد العنصر');
        }
        return $entity;
    }

}
