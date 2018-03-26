<?php

namespace TaskManagerBundle\Entity\Repository\Task;

use Doctrine\ORM\EntityRepository;
use TaskManagerBundle\Entity\Task;

class Repository extends EntityRepository
{

    private static $dataGrid;

    public function getDataGrid()
    {
        if(!self::$dataGrid) {
            self::$dataGrid = new DataGrid($this->getEntityManager());
        }
        return self::$dataGrid;
    }

    public function addTask($command, array $arguments = [], $category = null, $type = null, $description = null, $runEvery = null, \DateTime $dueAt = null)
    {
        $em = $this->getEntityManager();
        $task = new Task();
        $task   ->setCommand($command)
                ->setCommandArguments($arguments)
                ->setCategory($category)
                ->setType($type ? $type : Task::TYPE_QUEUED )
                ->setDueAt( $dueAt ? $dueAt : new \DateTime())
                ->setRunEvery($runEvery)
                ->setSortOrder($this->getMaxOrder() + 1)
                ->setDescription($description);

        $hash = $task->getComputedHash();
        $entity = $this->findOneByHash($hash);
        if(!empty($runEvery)) {
            if(!$entity) {
                $em->persist($task);
                $em->flush();
            }
        }else{
            if($entity) {
                if( in_array($entity->getStatus(), [Task::STATUS_RUNNING, Task::STATUS_FINISHED] ) ) {
                    //Renew the task
                    $entity->setStatus(Task::STATUS_WAITING);
                    $entity->setInsertedAt(new \DateTime());
                    $entity->setDueAt( $dueAt ? $dueAt : new \DateTime());
                    $entity->setFinishedAt(null);
                    $em->flush();
                }
            }else{
                $em->persist($task);
                $em->flush();
            }
        }
    }

    public function getMaxOrder()
    {
        $em = $this->getEntityManager();
        $params = [];
        $dql = "SELECT MAX(t.sortOrder)
        FROM TaskManagerBundle:Task t ";
        $query = $em->createQuery($dql);
        $query->setParameters($params);
        $max = $query->getSingleScalarResult();
        return intval($max);
    }

    public function getNumberOfRunningTasks()
    {
        $em = $this->getEntityManager();
        $dql = "SELECT COUNT(t.id)
        FROM TaskManagerBundle:Task t 
        WHERE t.status = :status ";
        $query = $em->createQuery($dql);
        $query->setParameters([
            'status' => Task::STATUS_RUNNING
        ]);
        return $query->getSingleScalarResult();
    }

    public function getWaitingTasksByType($type)
    {
        $em = $this->getEntityManager();
        $dql = "SELECT t
        FROM TaskManagerBundle:Task t 
        WHERE t.status = :status 
        AND t.type = :type
        AND t.dueAt <= :now
        ORDER BY t.sortOrder ASC ";
        $query = $em->createQuery($dql);
        $query->setParameters([
            'status'    => Task::STATUS_WAITING,
            'now'       => new \DateTime(),
            'type'      => $type
        ]);
        return $query->getResult();
    }

    public function isThereRunningTaskInTheSameTaskCategory(Task $task)
    {
        if($task->getCategory()) {
            $em = $this->getEntityManager();
            $dql = "SELECT COUNT(t.id)
                    FROM TaskManagerBundle:Task t 
                    WHERE t.status = :status
                    AND t.type = :type
                    AND t.category = :category ";
            $query = $em->createQuery($dql);
            $query->setParameters([
                'status' => Task::STATUS_RUNNING,
                'type' => Task::TYPE_RUN_ONE_PER_CATEGORY,
                'category' => $task->getCategory()
            ]);
            return $query->getSingleScalarResult() ? true : false;
        }else{
            return false;
        }
    }

}
