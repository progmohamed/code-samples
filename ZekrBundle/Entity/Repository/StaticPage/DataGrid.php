<?php

namespace ZekrBundle\Entity\Repository\StaticPage;

use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use AdminBundle\Classes\DataGrid as AdminDataGrid;

class DataGrid extends AdminDataGrid
{
    public function getGrid($paginator, $page, $rowsInPage)
    {
        $em = $this->getEntityManager();
        $dql = "SELECT s
        FROM ZekrBundle:StaticPage s
        LEFT JOIN s.translations st
        ";
        $query = $em->createQuery($dql);

        $pagination = $paginator->paginate(
            $query,
            $page,
            $rowsInPage,
            array('wrap-queries'=>true)
        );
        return $pagination;
    }
    protected function getEntityManager()
    {
        return $this->em;
    }
}