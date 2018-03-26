<?php

namespace LocaleBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use LocaleBundle\Entity\Language;
use LocaleBundle\Form\LanguageType;
use AdminBundle\Classes\AdminEvent;

/**
 * @Route("/language")
 */
class LanguageController extends Controller
{
    /**
     * @Route("/", name="locale_language_list")
     * @Security("has_role('ROLE_LOCALE_LANGUAGE_LIST')")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('LocaleBundle:Language');
        $entities = $repository->getGrid();
        return $this->render('LocaleBundle:Language:index.html.twig', [
            'entities' => $entities,
        ]);
    }


    /**
     * @Route("/new", name="locale_language_new")
     * @Security("has_role('ROLE_LOCALE_LANGUAGE_NEW')")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new Language();
        $form = $this->createForm(LanguageType::class, $entity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success', $this->get('translator')->trans('admin.messages.the_entry_has_been_added')
            );
            $this->get('common.service')->log($this->get('locale.service')->getName(), 'locale.log.add_language', ['%entity_id%'=>$entity->getId()], $this->getUser()->getId());
            return $this->redirectToRoute('locale_language_show', ['id'=>$entity->getId()]);

        }

        return $this->render('LocaleBundle:Language:new.html.twig', [
            'entity' => $entity,
            'form'   => $form->createView(),
        ]);
    }

    /**
     * @ParamConverter("entity", class="LocaleBundle:Language")
     * @Route("/show/{id}", name="locale_language_show")
     * @Security("has_role('ROLE_LOCALE_LANGUAGE_SHOW')")
     */
    public function showAction(Language $entity)
    {
        return $this->render('LocaleBundle:Language:show.html.twig', [
            'entity' => $entity
        ]);
    }

    /**
     * @ParamConverter("entity", class="LocaleBundle:Language")
     * @Route("/edit/{id}", name="locale_language_edit")
     * @Security("has_role('ROLE_LOCALE_LANGUAGE_EDIT')")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Language $entity)
    {

        $em = $this->getDoctrine()->getManager();
        $editForm = $this->createForm(LanguageType::class, $entity, ['edit'=>true]);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success', $this->get('translator')->trans('admin.messages.the_entry_has_been_updated')
            );
            $this->get('common.service')->log($this->get('locale.service')->getName(), 'locale.log.edit_language', ['%entity_id%'=>$entity->getId()], $this->getUser()->getId());
            return $this->redirectToRoute('locale_language_show', ['id' => $entity->getId()]);
        }

        return $this->render('LocaleBundle:Language:edit.html.twig', [
            'entity'      => $entity,
            'edit_form' => $editForm->createView(),
        ]);
    }


    /**
     * @Route("/delete", name="locale_language_delete")
     * @Security("has_role('ROLE_LOCALE_LANGUAGE_DELETE')")
     */
    public function deleteAction(Request $request)
    {
        $em = $this->get('doctrine')->getManager();
        $single = $request->query->get('single', false);
        $id = $request->query->get('id');
        $encodedRedirect = $request->query->get('redirect');
        $redirect = base64_decode( $encodedRedirect );
        if ($single) {
            return $this->redirectToRoute('locale_language_delete', [
                'id' => base64_encode(serialize([$id])),
                'redirect' => $encodedRedirect
            ]);
        } else {
            $repository = $em->getRepository('LocaleBundle:Language');
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
                            $this->get('common.service')->log($this->get('locale.service')->getName(), 'locale.log.delete_language', ['%entity_id%'=>$id], $this->getUser()->getId());
                        }
                    }catch(\Exception $e) {
                        $this->get('session')->getFlashBag()->add(
                            'danger', $e->getMessage()
                        );
                    }
                }
                return $this->redirectToRoute('locale_language_list');
            }else{
                $report = $repository->getDeleteRestrectionsByIds(
                    $bundleService,
                    $ids
                );
                return $this->render('LocaleBundle:Language:delete.html.twig', [
                    'report' => $report,
                    'redirect' => $redirect,
                ]);
            }
        }
    }

    /**
     * @Route("/batch", name="locale_language_batch")
     * @Security("has_role('ROLE_LOCALE_LANGUAGE_Delete')")
     */
    public function batchAction(Request $request)
    {
        $ids = $request->query->get('ids');
        $idx = explode(',', $ids);
        $action = $request->query->get('action');
        if ('delete' == $action) {
            return $this->redirectToRoute('locale_language_delete', [
                'id' => base64_encode(serialize($idx))
            ]);
        }
    }

}
