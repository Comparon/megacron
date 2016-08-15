<?php

namespace Comparon\SchedulingBundle\Facade;

use Comparon\SchedulingBundle\Model\TaskConfiguration;
use Cron\CronExpression;
use Symfony\Component\Console\Command\Command;;
use Symfony\Component\Process\Process;

class TaskProcessorFacade
{
    /** @var string */
    private $binDirPath;

    /** @var Command */
    private $command;

    /** @var TaskConfiguration */
    private $taskConfig;

    /**
     * @param string $binDirPath
     * @param Command $command
     * @param TaskConfiguration $taskConfig
     */
    public function __construct($binDirPath, Command $command, TaskConfiguration $taskConfig)
    {
        $this->binDirPath = $binDirPath;
        $this->command = $command;
        $this->taskConfig = $taskConfig;
    }

    /**
     * @return int|null
     */
    public function process()
    {
        if ($this->isDue() && !$this->isOverlapping()) {
            $args = implode(' ', $this->taskConfig->getParameters());
            $process = new Process($this->binDirPath . 'console ' . $this->command->getName() . ' ' . $args);
            $process->start();
            return $process->getPid();
        }
        return null;
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

