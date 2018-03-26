<?php

namespace TaskManagerBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use TaskManagerBundle\Entity\Task;

class TaskManagerCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('taskmanager:run')
            ->setDescription('Task Manager')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->removeOldTasks($input, $output);
        $this->executeTasks($input, $output);
    }

    private function removeOldTasks(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $date = new \DateTime();
        $date->modify('-1 day');
        $queryBuilder = $em->createQueryBuilder()
            ->delete('TaskManagerBundle:Task', 't')
            ->where('t.status = :statusFinished')
            ->andWhere('t.runEvery IS NULL')
            ->andWhere('t.finishedAt IS NOT NULL')
            ->andWhere('t.finishedAt < :days')
            ->setParameter('statusFinished', Task::STATUS_FINISHED)
            ->setParameter('days', $date);
        $queryBuilder->getQuery()->execute();
    }

    private function executeTasks(InputInterface $input, OutputInterface $output)
    {
        $maxSimultaneousRunningTasks = 5;
        $em = $this->getContainer()->get('doctrine')->getManager();
        $taskRepository = $em->getRepository('TaskManagerBundle:Task');
        $numRunningTasks = $taskRepository->getNumberOfRunningTasks(Task::STATUS_RUNNING);
        if ($numRunningTasks < $maxSimultaneousRunningTasks) {
            $tasks = $taskRepository->getWaitingTasksByType(Task::TYPE_RUN_IMMEDIATELY);
            if (sizeof($tasks)) {
                $this->executeTask($tasks[0], $input, $output);
            }else{
                $onePerCategoryRun = false;
                $tasks = $taskRepository->getWaitingTasksByType(Task::TYPE_RUN_ONE_PER_CATEGORY);
                foreach ($tasks as $task) {
                    if(!$taskRepository->isThereRunningTaskInTheSameTaskCategory($task)) {
                        $onePerCategoryRun = true;
                        $this->executeTask($task, $input, $output);
                        break;
                    }
                }
                if(!$onePerCategoryRun) {
                    $tasks = $taskRepository->getWaitingTasksByType(Task::TYPE_QUEUED);
                    if (sizeof($tasks)) {
                        $this->executeTask($tasks[0], $input, $output);
                    }
                }
            }
        }
    }

    private function executeTask(Task $task, InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        try {
            $task->setStatus(Task::STATUS_RUNNING);
            $task->setRunAt(new \DateTime());
            $em->flush();
            $command = $this->getApplication()->find($task->getCommand());
            $greetInput = new ArrayInput($task->getCommandArguments());
            $returnCode = $command->run($greetInput, $output);
        }catch(\Exception $e) {
            //TO-DO: logging errors...etc.
        }finally {
            if($task->getRunEvery()) {
                $task->setStatus(Task::STATUS_WAITING);
                $task->setRunAt(null);
                $task->renewDueAt();
            }else{
                $task->setStatus(Task::STATUS_FINISHED);
                $task->setFinishedAt(new \DateTime());
            }
            $em->flush();
        }
    }

}