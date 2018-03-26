<?php

namespace TaskManagerBundle\Entity\Repository\Task;

use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use AdminBundle\Classes\DataGrid as AdminDataGrid;
use TaskManagerBundle\Entity\Task;

class DataGrid extends AdminDataGrid
{
    public function getGrid($paginator, $page)
    {
        $em = $this->getEntityManager();
        $parameters = [];
        $dql = "SELECT t
        FROM TaskManagerBundle:Task t
        WHERE 1=1 ";

        $elementValue = $this->getFormDataElement('description');
        if($elementValue) {
            $dql .= "AND t.description LIKE :description ";
            $parameters['description'] = "%".$elementValue."%";
        };

        $elementValue = $this->getFormDataElement('category');
        if($elementValue) {
            $dql .= "AND t.category LIKE :category ";
            $parameters['category'] = "%".$elementValue."%";
        };

        $elementValue = $this->getFormDataElement('command');
        if($elementValue) {
            $dql .= "AND t.command LIKE :command ";
            $parameters['command'] = "%".$elementValue."%";
        };


        $elementValue = $this->getFormDataElement('status');
        if($elementValue) {
            $dql .= "AND t.status IN(:status) ";
            $parameters['status'] = $elementValue;
        }

        $dql .= "ORDER BY t.sortOrder ";
        $query = $em->createQuery($dql);
        if(sizeof($parameters)) {
            $query->setParameters($parameters);
        }

        $pagination = $paginator->paginate(
            $query,
            $page,
            30,
            ['wrap-queries' => true]
        );
        return $pagination;


        return $query->getResult();
    }

    public function getFilterForm($formFactory, $formActionUrl, $data = null, $options = [])
    {
        $form = $formFactory->createBuilder(FormType::class, $data, $options);
        $form
            ->setMethod('POST')
            ->setAction($formActionUrl)
            ->add('description', TextType::class, [
                'label' => 'task_mager.titles.task'
            ])
            ->add('category', TextType::class, [
                'label' => 'task_mager.titles.category'
            ])
            ->add('command', TextType::class, [
                'label' => 'task_mager.titles.command'
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'task_mager.titles.status',
                'choices'  => [
                    'task_mager.titles.status_watting' => Task::STATUS_WAITING,
                    'task_mager.titles.status_running' => Task::STATUS_RUNNING,
                    'task_mager.titles.status_finished' => Task::STATUS_FINISHED,
                ],
                'multiple' => true,
            ]);
        return $form->getForm();
    }
}