<?php

namespace LocaleBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use LocaleBundle\Entity\Country;
use LocaleBundle\Form\CountryType;
use AdminBundle\Classes\AdminEvent;

/**
 * @Route("/country")
 */
class CountryController extends Controller
{

    /**
     * @Route("/", name="locale_country")
     * @Security("has_role('ROLE_LOCAL_COUNTRY_LIST')")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $countryRepository = $em->getRepository('LocaleBundle:Country');
        $entities = $countryRepository->getGrid();
        return $this->render('LocaleBundle:Country:index.html.twig', [
            'entities' => $entities,
        ]);
    }

    /**
     * @Route("/new", name="locale_country_new")
     * @Security("has_role('ROLE_LOCAL_COUNTRY_NEW')")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $languages = $this->get('locale.service')->getContentLanguages();
        $entity = new Country();
        $form = $this->createForm(CountryType::class, $entity, ['languages'=> $languages]);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            foreach($languages as $language) {
                $entity->translate($language->getLocale())->setName($form->get('name_'.$language->getLocale())->getData());
            }
            $entity->mergeNewTranslations();

            $em->persist($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success', $this->get('translator')->trans('admin.messages.the_entry_has_been_added')
            );

            $this->get('common.service')->log($this->get('locale.service')->getName(), 'locale.log.add_country', ['%entity_id%' => $entity->getId()], $this->getUser()->getId());
            return $this->redirectToRoute('locale_country_show', ['id'=>$entity->getId()]);
        }

        return $this->render('LocaleBundle:Country:new.html.twig', [
            'entity'    => $entity,
            'form'      => $form->createView(),
        ]);
    }

    /**
     * @ParamConverter("entity", class="LocaleBundle:Country")
     * @Route("/show/{id}", name="locale_country_show")
     * @Security("has_role('ROLE_LOCAL_COUNTRY_SHOW')")
     */
    public function showAction(Country $entity)
    {
        return $this->render('LocaleBundle:Country:show.html.twig', [
            'entity' => $entity
        ]);
    }


    /**
     * @ParamConverter("entity", class="LocaleBundle:Country")
     * @Route("/edit/{id}", name="locale_country_edit")
     * @Security("has_role('ROLE_LOCAL_COUNTRY_EDIT')")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Country $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $languages = $this->get('locale.service')->getContentLanguages();
        $editForm = $this->createForm(CountryType::class, $entity, ['languages'=>$languages] );
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
                $this->get('common.service')->log($this->get('locale.service')->getName(), 'locale.log.edit_country', ['%entity_id%'=>$entity->getId()], $this->getUser()->getId());
                return $this->redirectToRoute('locale_country_show', ['id' => $entity->getId()]);
            }
        }else{
            foreach($languages as $language) {
                $editForm->get('name_'.$language->getLocale())->setData($entity->translate($language->getLocale(), false)->getName());
            }
        }

        return $this->render('LocaleBundle:Country:edit.html.twig', [
            'entity'        => $entity,
            'edit_form'     => $editForm->createView(),
        ]);

    }


    /**
     * @Route("/delete", name="locale_country_delete")
     * @Security("has_role('ROLE_LOCAL_COUNTRY_DELETE')")
     */
    public function deleteAction(Request $request)
    {
        $em = $this->get('doctrine')->getManager();
        $single = $request->query->get('single', false);
        $id = $request->query->get('id');

        $encodedRedirect = $request->query->get('redirect');
        $redirect = base64_decode( $encodedRedirect );
        if ($single) {
            return $this->redirectToRoute('locale_country_delete', [
                'id' => base64_encode(serialize([$id])),
                'redirect' => $encodedRedirect
            ]);
        } else {
            $repository = $em->getRepository('LocaleBundle:Country');
            $bundleService = $this->get('locale.service');
            $ids = unserialize(base64_decode($id));
            if (!is_array($ids) || empty($ids)) {
                throw $this->createNotFoundException( $this->get('translator')->trans('admin.titles.error_happened') );
            }
            if ('POST' == $request->getMethod()) {
                foreach($ids as $id) {
                    try {
                        $language = $repository->find($id);
                        if($language) {
                            $em->remove($language);
                            $em->flush();
                            $this->get('session')->getFlashBag()->add(
                                'success', $this->get('translator')->trans('admin.messages.the_entry_has_been_deleted').' '. $language
                            );
                            $this->get('common.service')->log($this->get('locale.service')->getName(), 'locale.log.add_country', ['%entity_id%'=>$id], $this->getUser()->getId());
                        }
                    }catch(\Exception $e) {
                        $this->get('session')->getFlashBag()->add(
                            'danger', $e->getMessage()
                        );
                    }
                }
                return $this->redirectToRoute('locale_country');
            }else{
                $report = $repository->getDeleteRestrectionsByIds(
                    $bundleService,
                    $ids
                );
                return $this->render('LocaleBundle:Country:delete.html.twig', [
                    'report' => $report,
                    'redirect' => $redirect,
                ]);
            }
        }
    }


    /**
     * @Route("/batch", name="locale_country_batch")
     * @Security("has_role('ROLE_LOCAL_COUNTRY_DELETE')")
     */
    public function batchAction(Request $request)
    {
        $ids = $request->query->get('ids');
        $idx = explode(',', $ids);
        $action = $request->query->get('action');
        if ('delete' == $action) {
            return $this->redirectToRoute('locale_country_delete', [
                'id' => base64_encode(serialize($idx))
            ]);
        }
    }

}
