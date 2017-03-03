<?php

namespace Comparon\MegacronBundle\Entity;

class MegaCronHistory
{
    private $id;

    private $cronJobName;


    protected $started;

    protected $stopped;

    public function getId()
    {
        return $this->id;
    }

    public function getCronJobName()
    {
        return $this->cronJobName;
    }

    public function setCronJobName($cronJobName)
    {
        $this->cronJobName = $cronJobName;
    }

    public function getStarted()
    {
        return $this->started;
    }

    public function getStopped()
    {
        return $this->stopped;
    }

    public function setStopped(\DateTime $stopped = null)
    {
        $this->stopped = $stopped;
    }

    public function onPrePersist()
    {
        $now = new \DateTime('now');
        if (!$this->started instanceof \DateTime) {
            $this->started = $now;
        }
    }

}