<?php

namespace Comparon\SchedulingBundle\Model;

class TaskConfiguration
{
    /** @var string */
    private $cronExpression;

    /** @var bool */
    private $isWithOverlapping;

    /** @var string[] */
    private $parameters;

    /**
     * @return boolean
     */
    public function isIsWithOverlapping()
    {
        return $this->isWithOverlapping;
    }

    /**
     * @param $isWithOverlapping
     * @return $this
     */
    public function setIsWithOverlapping($isWithOverlapping)
    {
        $this->isWithOverlapping = $isWithOverlapping;
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
}