<?php

namespace Comparon\MegacronBundle\Model;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

interface TaskInterface
{
    /**
     * @return TaskConfiguration[]
     */
    public function getTaskConfigurations();
}