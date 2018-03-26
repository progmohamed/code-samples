<?php

namespace TaskManagerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Task
 *
 * @ORM\Table(name="tm_task", uniqueConstraints={@ORM\UniqueConstraint(name="inx_hash", columns={"hash"})}, indexes={@ORM\Index(name="inx_sort_order", columns={"sort_order"}), @ORM\Index(name="inx_inserted_at", columns={"inserted_at"}), @ORM\Index(name="inx_run_at", columns={"run_at"}), @ORM\Index(name="inx_finished_at", columns={"finished_at"}), @ORM\Index(name="inx_category", columns={"category"}), @ORM\Index(name="inx_type", columns={"type"}), @ORM\Index(name="inx_due_at", columns={"due_at"}), @ORM\Index(name="inx_run_every", columns={"run_every"})})
 * @ORM\Entity(repositoryClass="TaskManagerBundle\Entity\Repository\Task\Repository")
 * @ORM\HasLifecycleCallbacks()
 */
class Task
{

    const STATUS_WAITING = 1;
    const STATUS_RUNNING = 2;
    const STATUS_FINISHED = 3;

    const TYPE_RUN_IMMEDIATELY = 1;
    const TYPE_RUN_ONE_PER_CATEGORY = 2;
    const TYPE_QUEUED = 3;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="inserted_at", type="datetime", nullable=false)
     */
    private $insertedAt;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="due_at", type="datetime", nullable=false)
     */
    private $dueAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="run_at", type="datetime", nullable=true)
     */
    private $runAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="finished_at", type="datetime", nullable=true)
     */
    private $finishedAt;

    /**
     * @var string
     *
     * @ORM\Column(name="command", type="text", length=65535, nullable=false)
     */
    private $command;


    /**
     * @var string
     *
     * @ORM\Column(name="command_arguments", type="text", length=65535, nullable=true)
     */
    private $commandArguments;

    /**
     * @var integer
     *
     * @ORM\Column(name="sort_order", type="integer", nullable=false)
     */
    private $sortOrder;

    /**
     * @var string
     *
     * @ORM\Column(name="category", type="string", length=200, nullable=true)
     */
    private $category;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    private $status;


    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="integer", nullable=false)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=true)
     */
    private $description;


    /**
     * @var string
     *
     * @ORM\Column(name="run_every", type="string", length=100, nullable=true)
     */
    private $runEvery;


    /**
     * @var string
     *
     * @ORM\Column(name="hash", type="string", length=50, nullable=false)
     */
    private $hash;



    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set insertedAt
     *
     * @param \DateTime $insertedAt
     *
     * @return Task
     */
    public function setInsertedAt($insertedAt)
    {
        $this->insertedAt = $insertedAt;

        return $this;
    }

    /**
     * Get insertedAt
     *
     * @return \DateTime
     */
    public function getInsertedAt()
    {
        return $this->insertedAt;
    }

    /**
     * Set runAt
     *
     * @param \DateTime $runAt
     *
     * @return Task
     */
    public function setRunAt($runAt)
    {
        $this->runAt = $runAt;

        return $this;
    }

    /**
     * Get runAt
     *
     * @return \DateTime
     */
    public function getRunAt()
    {
        return $this->runAt;
    }

    /**
     * Set finishedAt
     *
     * @param \DateTime $finishedAt
     *
     * @return Task
     */
    public function setFinishedAt($finishedAt)
    {
        $this->finishedAt = $finishedAt;

        return $this;
    }

    /**
     * Get finishedAt
     *
     * @return \DateTime
     */
    public function getFinishedAt()
    {
        return $this->finishedAt;
    }

    /**
     * Set command
     *
     * @param string $command
     *
     * @return Task
     */
    public function setCommand($command)
    {
        $this->command = $command;

        return $this;
    }

    /**
     * Get command
     *
     * @return string
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * Set commandArguments
     *
     * @param array $commandArguments
     *
     * @return Task
     */
    public function setCommandArguments(array $commandArguments)
    {
        if(sizeof($commandArguments)) {
            $this->commandArguments = serialize($commandArguments);
        }else{
            $this->commandArguments = null;
        }

        return $this;
    }

    /**
     * Get commandArguments
     *
     * @return array
     */
    public function getCommandArguments()
    {
        if($this->commandArguments) {
            return unserialize($this->commandArguments);
        }else{
            return [];
        }
    }

    /**
     * Set sortOrder
     *
     * @param integer $sortOrder
     *
     * @return Task
     */
    public function setSortOrder($sortOrder)
    {
        $this->sortOrder = $sortOrder;

        return $this;
    }

    /**
     * Get sortOrder
     *
     * @return integer
     */
    public function getSortOrder()
    {
        return $this->sortOrder;
    }

    /**
     * Set category
     *
     * @param string $category
     *
     * @return Task
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return Task
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set type
     *
     * @param integer $type
     *
     * @return Task
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Task
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set runEvery
     *
     * @param string $runEvery
     *
     * @return Task
     */
    public function setRunEvery($runEvery)
    {
        if($runEvery) {
            $this->runEvery = strtolower(str_replace('  ', ' ', $runEvery));
        }else{
            $this->runEvery = null;
        }
        return $this;
    }

    /**
     * Get runEvery
     *
     * @return string
     */
    public function getRunEvery()
    {
        return $this->runEvery;
    }

    /**
     * Set dueAt
     *
     * @param \DateTime $dueAt
     *
     * @return Task
     */
    public function setDueAt($dueAt)
    {
        $this->dueAt = $dueAt;

        return $this;
    }

    /**
     * Get dueAt
     *
     * @return \DateTime
     */
    public function getDueAt()
    {
        return $this->dueAt;
    }

    /**
     * Set hash
     *
     * @param string $hash
     *
     * @return Task
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * Get hash
     *
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    public function getComputedHash()
    {
        $values = [
            'command'           => $this->getCommand(),
            'commandArguments'  => $this->getCommandArguments(),
            'category'          => $this->getCategory(),
            'type'              => $this->getType(),
            'runPeriodically'   => $this->getRunEvery()
        ];
        return sha1(serialize($values));
    }

    private function getNextDueAt()
    {
        if($this->getRunEvery()) {
            list($letter, $number) = explode(" ", $this->getRunEvery());
            $now = new \DateTime();
            if('s' == $letter) {
                $now->add(new \DateInterval('PT'.$number.'S'));
            }
            if('m' == $letter) {
                $now->add(new \DateInterval('PT'.$number.'I'));
            }
            if('h' == $letter) {
                $now->add(new \DateInterval('PT'.$number.'H'));
            }
            return $now;
        }
    }

    public function renewDueAt()
    {
        if($this->getRunEvery()) {
            $this->setDueAt( $this->getNextDueAt() );
        }

        return $this;
    }

    private function validateOptions()
    {
        $thisClass = new \ReflectionClass(__CLASS__);
        $allConstants = $thisClass->getConstants();
        $statusConstants = array_filter($allConstants, function($k) {
            return substr($k, 0, 7) == 'STATUS_'? true : false;
        }, ARRAY_FILTER_USE_KEY);

        $typeConstants = array_filter($allConstants, function($k) {
            return substr($k, 0, 5) == 'TYPE_'? true : false;
        }, ARRAY_FILTER_USE_KEY );
        if (!in_array($this->getStatus(), $statusConstants)) {
            throw new \Exception('Invalid Task Status');
        }
        if (!in_array($this->getType(), $typeConstants)) {
            throw new \Exception('Invalid Task Type');
        }

        if($this->getRunEvery() !== null) {
            $errRunEvery = false;
            if(!is_string($this->getRunEvery())) {
                $errRunEvery = true;
            }else{
                if (!preg_match('/[shm] +\d+/i', $this->getRunEvery())) {
                    $errRunEvery = true;
                }
            }
            if($errRunEvery) {
                throw new \Exception('Invalid (RunEvery) Expression');
            }
        }
    }

    /**
     * @ORM\PrePersist()
     */
    public function beforeInsert()
    {
        $this->setHash( $this->getComputedHash() );
        if (!$this->getInsertedAt()) {
            $this->setInsertedAt(new \DateTime());
        }
        if (!$this->getDueAt()) {
            $this->setDueAt(new \DateTime());
        }
        if (!$this->getStatus()) {
            $this->setStatus(self::STATUS_WAITING);
        }
        $this->validateOptions();
    }

}
