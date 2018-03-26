<?php

namespace TaskManagerBundle\Service;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use TaskManagerBundle\Entity\Task;

class TaskManagerService implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function addTaskOnePerCategory($command, array $arguments = [], $category, $description = null, $runEvery = null, \DateTime $dueAt = null)
    {
        $em = $this->container->get('doctrine')->getManager();
        $taskRepository = $em->getRepository('TaskManagerBundle:Task');
        $taskRepository->addTask($command, $arguments, $category, Task::TYPE_RUN_ONE_PER_CATEGORY, $description, $runEvery, $dueAt);
    }

    public function addTaskRunImmediately($command, array $arguments = [], $description = null, $runEvery = null, \DateTime $dueAt = null)
    {
        $em = $this->container->get('doctrine')->getManager();
        $taskRepository = $em->getRepository('TaskManagerBundle:Task');
        $taskRepository->addTask($command, $arguments, null, Task::TYPE_RUN_IMMEDIATELY, $description, $runEvery, $dueAt);
    }

    public function addTaskQueued($command, array $arguments = [], $description = null, $runEvery = null, \DateTime $dueAt = null)
    {
        $em = $this->container->get('doctrine')->getManager();
        $taskRepository = $em->getRepository('TaskManagerBundle:Task');
        $taskRepository->addTask($command, $arguments, null, Task::TYPE_QUEUED, $description, $runEvery, $dueAt);
    }

}