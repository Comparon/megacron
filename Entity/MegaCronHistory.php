<?php

namespace Comparon\SchedulingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\HasLifecycleCallbacks
 * @ORM\MappedSuperclass
 */
abstract class MegaCronHistory
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="cronjob_name", type="string", length=255)
     */
    private $cronJobName;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="started", type="datetime")
     */
    protected $started;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="stopped", type="datetime", nullable=true)
     */
    protected $stopped;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getCronJobName()
    {
        return $this->cronJobName;
    }

    /**
     * @param string $cronJobName
     */
    public function setCronJobName($cronJobName)
    {
        $this->cronJobName = $cronJobName;
    }

    /**
     * @return \DateTime
     */
    public function getStarted()
    {
        return $this->started;
    }

    /**
     * @return \DateTime
     */
    public function getStopped()
    {
        return $this->stopped;
    }

    /**
     * @param \DateTime $stopped
     */
    public function setStopped(\DateTime $stopped = null)
    {
        $this->stopped = $stopped;
    }

    /**
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $now = new \DateTime('now');
        if (!$this->started instanceof \DateTime) {
            $this->started = $now;
        }
    }

}