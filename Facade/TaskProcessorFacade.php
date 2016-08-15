<?php

namespace Comparon\SchedulingBundle\Facade;

use Comparon\SchedulingBundle\Model\TaskConfiguration;
use Cron\CronExpression;
use Symfony\Bundle\FrameworkBundle\Command\Command;
use Symfony\Component\Process\Process;

class TaskProcessorFacade
{
    /** @var string */
    private $consolePath;

    /** @var Command */
    private $command;

    /** @var TaskConfiguration */
    private $taskConfig;

    /**
     * @param string $consolePath
     * @param Command $command
     * @param TaskConfiguration $taskConfig
     */
    public function __construct($consolePath, Command $command, TaskConfiguration $taskConfig)
    {
        $this->consolePath = $consolePath;
        $this->command = $command;
        $this->taskConfig = $taskConfig;
    }

    public function process()
    {
        if ($this->isDue() && !$this->isOverlapping()) {
            $args = implode(' ', $this->config->getParameters());
            $process = new Process($this->consolePath . ' ' . $this->command->getName() . ' ' . $args);
            $process->start();
        }
    }

    /**
     * @return bool
     */
    private function isDue()
    {
        $expression = $this->taskConfig->getCronExpression();
        if (CronExpression::isValidExpression($expression)) {
            $cron = CronExpression::factory($expression);
            return $cron->isDue(new \DateTime('now'));
        }
        // TODO: Log
        return false;
    }

    /**
     * @return bool
     */
    private function isOverlapping()
    {
        if (!$this->taskConfig->isWithOverlapping()) {
            $key = $this->command->getName() . $this->taskConfig->getCronExpression();
        }
        return false;
    }
}

