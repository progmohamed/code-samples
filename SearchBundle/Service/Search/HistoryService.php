<?php

namespace SearchBundle\Service\Search;

use CommonBundle\Classes\PublicService;
use SearchBundle\Entity\History;

class HistoryService extends PublicService
{

    public function getName()
    {
        return "كلمات البحث";
    }


    public function addString($string)
    {
        $em = $this->container->get('doctrine')->getManager();
        $searchHistory = $em->getRepository("SearchBundle:History");
        if ($entity = $searchHistory->findOneBy(['string' => $string])) {
            /** @var $string History */
            $entity->setSum($entity->getSum() + 1);
        } else {
            $history = new History();
            $history->setString($string);
            $history->setSum(1);
            $em->persist($history);
        }
        $em->flush();

        //$this->get('history.service')->addString('اي بتنجان');
    }
}