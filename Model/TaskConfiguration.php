<?php

namespace Comparon\SchedulingBundle\Model;

use Comparon\SchedulingBundle\Entity\MegaCronHistory;

class TaskConfiguration
{
    /** @var string */
    private $cronExpression;

    /**
     * Indicates if the command should overlap itself.
     *
     * @var bool
     */
    private $withOverlapping = true;

    /** @var bool */
    private $useMegaCronHistoryEntries = false;

    /** @var string[] */
    private $parameters = [];

    /**
     * @return bool
     */
    public function isWithOverlapping()
    {
        return $this->withOverlapping;
    }

    /**
     * @param bool $withOverlapping
     * @return $this
     */
    public function setWithOverlapping($withOverlapping)
    {
        $this->withOverlapping = $withOverlapping;
        return $this;
    }

    /**
     * @return string
     */
    public function getCronExpression()
    {
        return $this->cronExpression;
    }

    /**
     * @param $cronExpression
     * @return $this
     */
    public function setCronExpression($cronExpression)
    {
        $this->cronExpression = $cronExpression;
        return $this;
    }

    /**
     * @param $parameter
     * @return $this
     */
    public function addParameter($parameter)
    {
        $this->parameters[] = $parameter;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @return boolean
     */
    public function getUseMegaCronHistoryEntries()
    {
        return $this->useMegaCronHistoryEntries;
    }

    public function enableMegaCronHistoryEntries()
    {
        $this->useMegaCronHistoryEntries = true;
    }
}
