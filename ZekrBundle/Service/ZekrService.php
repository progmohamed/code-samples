<?php

namespace ZekrBundle\Service;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ZekrService implements ContainerAwareInterface
{

    use ContainerAwareTrait;

    private $category = [];
    private $videoType = [];
    private $rewaya = [];
    private $person = [];
    private $juz = [];
    private $hizb = [];
    private $surah = [];
    private $collection = [];

    public function getCategoryBySlug($slug)
    {
        if(!isset($this->category[$slug])) {
            $em = $this->container->get('doctrine')->getManager();
            $entity = $em->getRepository('ZekrBundle:Category')->findOneBySlug($slug);
            if (!$entity) {
                throw new NotFoundHttpException();
            }
            $this->category[$slug] = $entity;
        }
        return $this->category[$slug];
    }

    public function getVideoTypeBySlug($slug)
    {
        if(!isset($this->videoType[$slug])) {
            $em = $this->container->get('doctrine')->getManager();
            $entity = $em->getRepository('ZekrBundle:VideoType')->findOneBySlug($slug);
            if (!$entity) {
                throw new NotFoundHttpException();
            }
            $this->videoType[$slug] = $entity;
        }
        return $this->videoType[$slug];
    }

    public function getRewayaBySlug($slug)
    {
        if(!isset($this->rewaya[$slug])) {
            $em = $this->container->get('doctrine')->getManager();
            $entity = $em->getRepository('ZekrBundle:Rewaya')->findOneBySlug($slug);
            if (!$entity) {
                throw new NotFoundHttpException();
            }
            $this->rewaya[$slug] = $entity;
        }
        return $this->rewaya[$slug];
    }

    public function getPersonBySlug($slug)
    {
        if(!isset($this->person[$slug])) {
            $em = $this->container->get('doctrine')->getManager();
            $entity = $em->getRepository('ZekrBundle:Person')->findOneBySlug($slug);
            if (!$entity) {
                throw new NotFoundHttpException();
            }
            $this->person[$slug] = $entity;
        }
        return $this->person[$slug];
    }

    public function getJuzBySlug($slug)
    {
        if(!isset($this->juz[$slug])) {
            $em = $this->container->get('doctrine')->getManager();
            $entity = $em->getRepository('ZekrBundle:Juz')->findOneBySlug($slug);
            if (!$entity) {
                throw new NotFoundHttpException();
            }
            $this->juz[$slug] = $entity;
        }
        return $this->juz[$slug];
    }

    public function getHizbBySlug($slug)
    {
        if(!isset($this->hizb[$slug])) {
            $em = $this->container->get('doctrine')->getManager();
            $entity = $em->getRepository('ZekrBundle:Hizb')->findOneBySlug($slug);
            if (!$entity) {
                throw new NotFoundHttpException();
            }
            $this->hizb[$slug] = $entity;
        }
        return $this->hizb[$slug];
    }

    public function getSurahBySlug($slug)
    {
        if(!isset($this->surah[$slug])) {
            $em = $this->container->get('doctrine')->getManager();
            $entity = $em->getRepository('ZekrBundle:Surah')->findOneBySlug($slug);
            if (!$entity) {
                throw new NotFoundHttpException();
            }
            $this->surah[$slug] = $entity;
        }
        return $this->surah[$slug];
    }

    public function getCollectionBySlug($slug)
    {
        if(!isset($this->collection[$slug])) {
            $em = $this->container->get('doctrine')->getManager();
            $entity = $em->getRepository('ZekrBundle:Collection')->findOneBySlug($slug);
            if (!$entity) {
                throw new NotFoundHttpException();
            }
            $this->collection[$slug] = $entity;
        }
        return $this->collection[$slug];
    }

}