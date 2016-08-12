<?php

namespace Comparon\SchedulingBundle\Model;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

interface TaskInterface
{
    /**
     * @return TaskConfiguration[]
     */
    public function getTaskConfigurations();

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function execute(InputInterface $input, OutputInterface $output);
}