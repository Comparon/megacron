<?php

namespace Comparon\SchedulingBundle\Model;

interface TaskInterface
{
    /**
     * @return TaskConfiguration[]
     */
    public function getTaskConfigurations();
}