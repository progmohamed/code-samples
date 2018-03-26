<?php

namespace ApiBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\Route;
use ZekrBundle\Entity\ContactUs;
use ZekrBundle\Entity\Newsletter;
use JMS\Serializer\SerializationContext;
use FOS\RestBundle\Controller\FOSRestController;

class FragmentController extends FOSRestController
{
    public function getSurahAction()
    {
        $data = [];
        $results = $this->getDoctrine()->getManager()->getRepository('ZekrBundle:Surah')->findBy([], ['sortOrder' => 'ASC']);
        $data['count'] = count($results);
        $data['results'] = $results;
        return $data;
    }

    public function getJuzAction()
    {
        $data = [];
        $results = $this->getDoctrine()->getManager()->getRepository('ZekrBundle:Juz')->findBy([], ['sortOrder' => 'ASC']);
        $data['count'] = count($results);
        $data['results'] = $results;
        return $data;
    }

    public function getHizbAction()
    {
        $data = [];
        $results = $this->getDoctrine()->getManager()->getRepository('ZekrBundle:Hizb')->findBy([], ['sortOrder' => 'ASC']);
        $data['count'] = count($results);
        $data['results'] = $results;
        return $data;
    }

    public function getPersonsAction(Request $request)
    {
        $page = (int)$request->query->get('page', 1);
        $limit = (int)$request->query->get('limit', 20);
        $sortField = $request->query->get('sort_field', 'id');
        $sortDirection = $request->query->get('sort_direction', 'DESC');

        $results = $this->getDoctrine()->getManager()->getRepository('ZekrBundle:Person')->getPersons($this->get('knp_paginator'), $page, $limit, $sortField, $sortDirection);
        $results->sortField = $sortField;
        $results->sortDirection = $sortDirection;

        $context = new SerializationContext();
        $context->setGroups(['default', 'sortable']);

        $view = $this->view($results, 200);
        $view->setSerializationContext($context);

        return $this->handleView($view);

    }

    public function getRewayaAction(Request $request)
    {
        $page = (int)$request->query->get('page', 1);
        $limit = (int)$request->query->get('limit', 20);

        $results = $this->getDoctrine()->getManager()->getRepository('ZekrBundle:Rewaya')->getRewaya($this->get('knp_paginator'), $page, $limit);

        $context = new SerializationContext();
        $context->setGroups(['default']);

        $view = $this->view($results, 200);
        $view->setSerializationContext($context);
        return $this->handleView($view);
    }

    /**
     * @Route("video_type")
     */
    public function getVideoTypeAction(Request $request)
    {
        $page = (int)$request->query->get('page', 1);
        $limit = (int)$request->query->get('limit', 20);
        $sortField = $request->query->get('sort_field', 'id');
        $sortDirection = $request->query->get('sort_direction', 'DESC');

        $results = $this->getDoctrine()->getManager()->getRepository('ZekrBundle:VideoType')->getVideoType($this->get('knp_paginator'), $page, $limit, $sortField, $sortDirection);
        $results->sortField = $sortField;
        $results->sortDirection = $sortDirection;

        $context = new SerializationContext();
        $context->setGroups(['default', 'sortable']);

        $view = $this->view($results, 200);
        $view->setSerializationContext($context);
        return $this->handleView($view);
    }

    /**
     * @Route("{projectSlug}/collections")
     */
    public function getCollectionsAction(Request $request, $projectSlug)
    {
        $page = (int)$request->query->get('page', 1);
        $limit = (int)$request->query->get('limit', 20);

        $results = $this->getDoctrine()->getManager()->getRepository('ZekrBundle:Collection')->getCollections($this->get('knp_paginator'), $page, $limit, $projectSlug);

        $context = new SerializationContext();
        $context->setGroups(['default']);

        $view = $this->view($results, 200);
        $view->setSerializationContext($context);
        return $this->handleView($view);
    }

    /**
     * @Route("{projectSlug}/selected_collections")
     */
    public function getSelectedCollectionsAction(Request $request, $projectSlug)
    {
        $page = (int)$request->query->get('page', 1);
        $limit = (int)$request->query->get('limit', 20);

        $results = $this->getDoctrine()->getManager()->getRepository('ZekrBundle:Collection')->getselectedCollections($this->get('knp_paginator'), $page, $limit, $projectSlug);

        $context = new SerializationContext();
        $context->setGroups(['default']);

        $view = $this->view($results, 200);
        $view->setSerializationContext($context);
        return $this->handleView($view);
    }

    public function getCategoriesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $countResult = $em->getRepository('ZekrBundle:Category')->getCategoriesCount();
        $results = $em->getRepository('ZekrBundle:Category')->getApiCategories(null);
        $data['count'] = $countResult;
        $data['results'] = $results;
        return $data;
    }

    /**
     * @Route("static_page/{pageSlug}")
     */
    public function getStaticPageAction($pageSlug)
    {
        $results = $this->getDoctrine()->getManager()->getRepository('ZekrBundle:StaticPage')->findOneBySlug($pageSlug);
        $data['count'] = count($results);
        $data['results'] = $results;
        return $data;
    }

    /**
     * @Route("newsletter")
     */
    public function postNewsletterAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $name = $request->request->get('name');
        $email = $request->request->get('email');
        try {
            $entity = new Newsletter();
            $entity->setName($name)
                ->setEmail($email)
                ->setLocale($request->getLocale());
            $em->persist($entity);
            $em->flush();
            $data['status'] = true;
            $data['message'] = 'success';
        } catch (\Exception $e) {
            $data['status'] = false;
            $data['message'] = $e->getMessage();
        }
        return $data;
    }

    /**
     * @Route("contact_us")
     */
    public function postContactUsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $name = $request->request->get('name');
        $email = $request->request->get('email');
        $title = $request->request->get('title');
        $message = $request->request->get('message');
        try {
            $entity = new ContactUs();
            $entity->setName($name)
                ->setEmail($email)
                ->setTitle($title)
                ->setMessage($message)
                ->setLocale($request->getLocale());
            $em->persist($entity);
            $em->flush();
            $data['status'] = true;
            $data['message'] = 'success';
        } catch (\Exception $e) {
            $data['status'] = false;
            $data['message'] = $e->getMessage();
        }
        return $data;
    }
}
