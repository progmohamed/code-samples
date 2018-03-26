<?php

namespace LocaleBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use LocaleBundle\Entity\Dialect;
use LocaleBundle\Entity\DialectTranslation;
use LocaleBundle\Form\DialectType;
use AdminBundle\Classes\AdminEvent;

/**
 * @Route("/dialect")
 */
class DialectController extends Controller
{
    /**
     * @Route("/", name="locale_dialect_list")
     * @Security("has_role('ROLE_LOCALE_DIALECT_LIST')")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('LocaleBundle:Dialect');
        $dataGrid = $repository->getDataGrid();

        $form = $dataGrid->getFilterForm(
            $this->container->get('form.factory'),
            $this->generateUrl('locale_dialect_list')
        );

        if ('POST' == $request->getMethod()) {
            $form->handleRequest($request);
            return $this->redirectToRoute('locale_dialect_list', [
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

        return $this->render('LocaleBundle:Dialect:index.html.twig', [
            'entities' => $entities,
            'formFilter' => $form->createView(),
        ]);
    }


    /**
     * @Route("/new", name="locale_dialect_new")
     * @Security("has_role('ROLE_LOCALE_DIALECT_NEW')")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $languages = $this->get('locale.service')->getContentLanguages();
        $entity = new Dialect();
        $form = $this->createForm(DialectType::class, $entity, ['languages'=> $languages]);
        $form->handleRequest($request);
        $validator = $this->get('validator');

        if ($form->isSubmitted()) {
            foreach ($languages as $language) {
                $transEntity = new DialectTranslation();
                $transEntity->setLocale($language->getLocale())
                    ->setName($form->get('name_' . $language->getLocale())->getData())
                    ->setTranslatable($entity);
                $errors = $validator->validate($transEntity);
                if ($errors) {
                    foreach ($errors as $error) {
                        $form->get($error->getPropertyPath() . '_' . $language->getLocale())->addError(new \Symfony\Component\Form\FormError($error->getMessage()));
                    }
                }
            }

            if ($form->isValid()) {

                foreach ($languages as $language) {
                    $entity->translate($language->getLocale())->setLanguage($language->getId());
                    $entity->translate($language->getLocale())->setName($form->get('name_' . $language->getLocale())->getData());
                }
                $entity->mergeNewTranslations();

                $em->persist($entity);
                $em->flush();

                $this->get('session')->getFlashBag()->add(
                    'success', $this->get('translator')->trans('admin.messages.the_entry_has_been_added')
                );
                $this->get('common.service')->log($this->get('locale.service')->getName(), 'locale.log.add_dialect', ['%entity_id%'=>$entity->getId()], $this->getUser()->getId());
                return $this->redirectToRoute('locale_dialect_show', ['id' => $entity->getId()]);
            }
        }

        return $this->render('LocaleBundle:Dialect:new.html.twig', [
            'entity'    => $entity,
            'form'      => $form->createView(),
        ]);
    }


    /**
     * @ParamConverter("entity", class="LocaleBundle:Dialect")
     * @Route("/show/{id}", name="locale_dialect_show")
     * @Security("has_role('ROLE_LOCALE_DIALECT_SHOW')")
     */
    public function showAction(Dialect $entity)
    {
        $em = $this->getDoctrine()->getManager();
        return $this->render('LocaleBundle:Dialect:show.html.twig', [
            'entity' => $entity,
        ]);
    }


    /**
     * @ParamConverter("entity", class="LocaleBundle:Dialect")
     * @Route("/edit/{id}", name="locale_dialect_edit")
     * @Security("has_role('ROLE_LOCALE_DIALECT_EDIT')")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Dialect $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $languages = $this->get('locale.service')->getContentLanguages();
        $editForm = $this->createForm(DialectType::class, $entity, ['languages'=>$languages]);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted()) {
            if($editForm->isValid()) {
                foreach($languages as $language) {
                    $entity->translate($language->getLocale(), false)->setName($editForm->get('name_'.$language->getLocale())->getData());
                }
                $entity->mergeNewTranslations();
                $em->flush();
                $this->get('session')->getFlashBag()->add(
                    'success', $this->get('translator')->trans('admin.messages.the_entry_has_been_updated')
                );
                $this->get('common.service')->log($this->get('locale.service')->getName(), 'locale.log.edit_dialect', ['%entity_id%'=>$entity->getId()], $this->getUser()->getId());
                return $this->redirectToRoute('locale_dialect_show', ['id' => $entity->getId()]);
            }
        }else{
            foreach($languages as $language) {
                $editForm->get('name_'.$language->getLocale())->setData($entity->translate($language->getLocale(), false)->getName());
            }
        }

        return $this->render('LocaleBundle:Dialect:edit.html.twig', [
            'entity'        => $entity,
            'edit_form'     => $editForm->createView(),
        ]);
    }

    /**
     * @Route("/delete", name="locale_dialect_delete")
     * @Security("has_role('ROLE_LOCALE_DIALECT_DELETE')")
     */
    public function deleteAction(Request $request)
    {
        $em = $this->get('doctrine')->getManager();
        $single = $request->query->get('single', false);
        $id = $request->query->get('id');
        $encodedRedirect = $request->query->get('redirect');
        $redirect = base64_decode( $encodedRedirect );
        if ($single) {
            return $this->redirectToRoute('locale_dialect_delete', [
                'id' => base64_encode(serialize([$id])),
                'redirect' => $encodedRedirect
            ]);
        } else {
            $repository = $em->getRepository('LocaleBundle:Dialect');
            $bundleService = $this->get('locale.service');
            $ids = unserialize(base64_decode($id));
            if (!is_array($ids) || empty($ids)) {
                throw $this->createNotFoundException( $this->get('translator')->trans('admin.titles.error_happened') );
            }
            if ('POST' == $request->getMethod()) {
                foreach($ids as $id) {
                    try {
                        $dialect = $repository->find($id);
                        if($dialect) {
                            $em->remove($dialect);
                            $em->flush();
                            $this->get('session')->getFlashBag()->add(
                                'success', $this->get('translator')->trans('admin.messages.the_entry_has_been_deleted').' '. $dialect
                            );
                            $this->get('common.service')->log($this->get('locale.service')->getName(), 'locale.log.delete_dialect', ['%entity_id%'=>$id], $this->getUser()->getId());
                        }
                    }catch(\Exception $e) {
                        $this->get('session')->getFlashBag()->add(
                            'danger', $e->getMessage()
                        );
                    }
                }
                return $this->redirectToRoute('locale_dialect_list');
            }else{
                $report = $repository->getDeleteRestrectionsByIds(
                    $bundleService,
                    $ids
                );
                return $this->render('LocaleBundle:Dialect:delete.html.twig', [
                    'report' => $report,
                    'redirect' => $redirect,
                ]);
            }
        }
    }


    /**
     * @Route("/batch", name="locale_dialect_batch")
     * @Security("has_role('ROLE_LOCALE_DIALECT_DELETE')")
     */
    public function batchAction(Request $request)
    {
        $ids = $request->query->get('ids');
        $idx = explode(',', $ids);
        $action = $request->query->get('action');
        if ('delete' == $action) {
            return $this->redirectToRoute('locale_dialect_delete', [
                'id' => base64_encode(serialize($idx))
            ]);
        }
    }

}
