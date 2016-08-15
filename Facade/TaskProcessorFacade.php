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

    public function process()
    {
        $pidFileDir = $this->binDirPath
            . '..' . DIRECTORY_SEPARATOR
            . 'var' . DIRECTORY_SEPARATOR
            . 'comparon_scheduling' . DIRECTORY_SEPARATOR;

        if (!file_exists($pidFileDir)) {
            if (!mkdir($pidFileDir)) {
                throw new Exception("Could not create directory {$pidFileDir}");
            }
        }

        $processHash = sha1($this->command->getName() . $this->taskConfig->getCronExpression());
        $pidFilePath = $pidFileDir . $processHash . '.pid';

        if($this->isDue()) {
            $args = implode(' ', $this->taskConfig->getParameters());
            $processCmd = trim($this->binDirPath . 'console ' . $this->command->getName() . ' ' . $args);
            $processCmdSuffix = ' > /dev/null 2>/dev/null &';
            if (file_exists($pidFilePath) && $this->taskConfig->isWithOverlapping()) {
                unlink($pidFilePath);
            }

            if (!$this->taskConfig->isWithOverlapping()) {
                if (file_exists($pidFilePath)) {
                    $pid = intval(file_get_contents($pidFilePath));
                    $result = shell_exec("ps -fp {$pid}");
                    if(strpos($result, $processCmd) !== false) {
                        return;
                    }
                }

                file_put_contents($pidFilePath, '');
                $processCmdSuffix .= ' echo $! >> ' . $pidFilePath;
            }
            shell_exec($processCmd . $processCmdSuffix);
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
}

