<?php

namespace AdminBundle\Controller;

use Symfony\Component\Form\Forms;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use AdminBundle\Entity\User;
use AdminBundle\Form\UserType;

/**
 * @Route("/user")
 */
class UserController extends Controller
{
    /**
     * @Route("/", name="admin_user")
     * @Security("has_role('ROLE_ADMIN_USER_INDEX')")
     */
    public function listAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $userRepository = $em->getRepository('AdminBundle:User');
        $dataGrid = $userRepository->getDataGrid();
        $form = $dataGrid->getFilterForm(
            $this->container->get('form.factory'),
            $this->generateUrl('admin_user')
        );

        if ('POST' == $request->getMethod()) {
            $form->handleRequest($request);
            return $this->redirectToRoute('admin_user', [
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
            $request->query->getInt('page', 1),
            $request->getLocale()
        );
        return $this->render('AdminBundle:User:index.html.twig', [
            'entities' => $entities,
            'formFilter' => $form->createView(),
        ]);
    }


    /**
     * @Route("/new", name="admin_user_new")
     * @Security("has_role('ROLE_ADMIN_USER_ADD')")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $entity = new User();
        $entity->setEnabled(true);
        $entity->setDateOfBirth(new \DateTime());
        $countries = $this->get('locale.service')->getCountries($request->getLocale());
        $form = $this->createForm(UserType::class, $entity, ['countries' => $countries]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userManager = $this->container->get('fos_user.user_manager');
            $userManager->updateUser($entity);

            $this->get('session')->getFlashBag()->add(
                'success', $this->get('translator')->trans('admin.messages.the_entry_has_been_added')
            );
            $this->get('common.service')->log($this->get('admin.service')->getName(), 'admin.log.add_user', ['%entity_id%'=>$entity->getId()], $this->getUser()->getId());
            return $this->redirectToRoute('admin_user_show', ['id' => $entity->getId()]);
        }

        return $this->render('AdminBundle:User:new.html.twig', [
            'entity' => $entity,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @ParamConverter("entity", class="AdminBundle:User")
     * @Route("/show/{id}", name="admin_user_show")
     * @Security("has_role('ROLE_ADMIN_USER_SHOW')")
     */
    public function showAction(Request $request, User $entity)
    {
        $country = $this->get('locale.service')->getCountryArrayById($entity->getCountryId(), $request->getLocale());
        return $this->render('AdminBundle:User:show.html.twig', [
            'entity' => $entity,
            'country' => $country,
        ]);
    }

    /**
     * @Route("/show-profile", name="admin_user_show_profile")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function showProfileAction(Request $request)
    {
        $country = $this->get('locale.service')->getCountryArrayById($this->getUser()->getCountryId(), $request->getLocale());

        return $this->render('AdminBundle:User:showProfile.html.twig', [
            'entity' => $this->getUser(),
            'country' => $country,
        ]);
    }


    /**
     * @ParamConverter("entity", class="AdminBundle:User")
     * @Route("/edit/{id}", name="admin_user_edit")
     * @Security("has_role('ROLE_ADMIN_USER_EDIT')")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, User $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $countries = $this->get('locale.service')->getCountries($request->getLocale());
        $editForm = $this->createForm(UserType::class, $entity, ['countries' => $countries, 'entity' => $entity, 'profile' => ($this->getUser()->getId() == $entity->getId())]);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            if ($this->getUser()->getId() == $entity->getId()) {
                $entity->setEnabled(true);
            }

            $userManager = $this->container->get('fos_user.user_manager');
            $userManager->updateUser($entity);

            if ($editForm->get('removePhoto')->getData()) {
                $entity->deleteCurrentPhoto();
                $em->flush();
            }

            $this->get('session')->getFlashBag()->add(
                'success', $this->get('translator')->trans('admin.messages.the_entry_has_been_updated')
            );
            $this->get('common.service')->log($this->get('admin.service')->getName(), 'admin.log.edit_user', ['%entity_id%'=>$entity->getId()], $this->getUser()->getId());
            return $this->redirectToRoute('admin_user_show', ['id' => $entity->getId()]);
        }

        return $this->render('AdminBundle:User:edit.html.twig', [
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
        ]);
    }


    /**
     * @Route("/edit-profile", name="admin_user_edit_profile")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function editProfileAction(Request $request)
    {
        $em = $this->get('doctrine')->getManager();
        $id = $this->getUser()->getId();
        $entity = $this->getEntityById($id);
        $countries = $this->get('locale.service')->getCountries($request->getLocale());
        $editForm = $this->createForm(UserType::class, $entity, ['countries' => $countries, 'entity' => $entity, 'profile' => true]);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $userManager = $this->container->get('fos_user.user_manager');
            $userManager->updateUser($entity);

            if ($editForm->get('removePhoto')->getData()) {
                $entity->deleteCurrentPhoto();
                $em->flush();
            }

            $this->get('session')->getFlashBag()->add(
                'success', $this->get('translator')->trans('admin.messages.the_entry_has_been_updated')
            );
            $this->get('common.service')->log($this->get('admin.service')->getName(), 'admin.log.edit_profile', null, $this->getUser()->getId());
            return $this->redirectToRoute('admin_user_show_profile');
        }

        return $this->render('AdminBundle:User:editProfile.html.twig', [
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
        ]);
    }

    /**
     * @Route("/delete", name="admin_user_delete")
     * @Security("has_role('ROLE_ADMIN_USER_DELETE')")
     */
    public function deleteAction(Request $request)
    {
        $em = $this->get('doctrine')->getManager();
        $single = $request->query->get('single', false);
        $id = $request->query->get('id');
        $encodedRedirect = $request->query->get('redirect');
        $redirect = base64_decode( $encodedRedirect );
        if ($single) {
            return $this->redirectToRoute('admin_user_delete', [
                'id' => base64_encode(serialize([$id])),
                'redirect' => $encodedRedirect
            ]);
        } else {
            $repository = $entity = $em->getRepository('AdminBundle:User');
            $bundleService = $this->get('admin.service');
            $ids = unserialize(base64_decode($id));
            if (!is_array($ids) || empty($ids)) {
                throw $this->createNotFoundException($this->get('translator')->trans('admin.titles.error_happened'));
            }
            if ('POST' == $request->getMethod()) {
                foreach($ids as $id) {
                    try {
                        $user = $repository->find($id);
                        if($user) {
                            $em->remove($user);
                            $em->flush();
                            $this->get('session')->getFlashBag()->add(
                                'success', $this->get('translator')->trans('admin.messages.the_entry_has_been_deleted').' '.$user->getUsername()
                            );
                            $this->get('common.service')->log($this->get('admin.service')->getName(), 'admin.log.delete_user', ['%entity_id%'=>$user->getId()], $this->getUser()->getId());
                        }
                    }catch(\Exception $e) {
                        $this->get('session')->getFlashBag()->add(
                            'danger', $e->getMessage()
                        );
                    }
                }
                return $this->redirectToRoute('admin_user');
            }else{
                $report = $repository->getDeleteRestrectionsByIds(
                    $bundleService,
                    $ids
                );
                return $this->render('AdminBundle:User:delete.html.twig', [
                    'report' => $report,
                    'redirect' => $redirect,
                ]);
            }
        }
    }


    /**
     * @Route("/batch", name="admin_user_batch")
     * @Security("has_role('ROLE_ADMIN_USER_DELETE')")
     */
    public function batchAction(Request $request)
    {
        $ids = $request->query->get('ids');
        $idx = explode(',', $ids);
        $action = $request->query->get('action');
        if('delete' == $action) {
            return $this->redirectToRoute('admin_user_delete', [
                'id' => base64_encode(serialize($idx))
            ]);
        }else{
            foreach($idx as $id) {
                try {
                    if ('activate' == $action) {
                        $this->activateUserById($id, true);
                    } elseif ('deactivate' == $action) {
                        $this->activateUserById($id, false);
                    }
                }catch(\Exception $e) {
                    $this->get('session')->getFlashBag()->add(
                        'danger', $e->getMessage()
                    );
                }
            }
            return $this->redirectToRoute('admin_user');
        }
    }

    private function activateUserById($id, bool $status)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var User $entity */
        $entity = $this->getEntityById($id);
        $entity->setEnabled($status);
        $em->flush();
    }

    private function getEntityById($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AdminBundle:User')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException($this->get('translator')->trans('admin.messages.unable_to_find_the_entity'));
        }
        return $entity;
    }

}
