<?php

namespace Comparon\SchedulingBundle\Helper;

use Comparon\SchedulingBundle\Model\TaskConfiguration;
use Cron\CronExpression;
use Symfony\Component\Console\Command\Command;;
use Symfony\Component\Process\Process;

class TaskProcessorHelper
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
        $pidFileDir = $this->getPidFileDir();
        $this->createDir($pidFileDir);

        $processHash = sha1($this->command->getName() . $this->taskConfig->getCronExpression());
        $pidFilePath = $pidFileDir . $processHash . '.pid';

        if($this->isDue()) {
            $args = '';
            if (count($this->taskConfig->getParameters()) > 0) {
                $args = implode(' ', $this->taskConfig->getParameters());
            }

            $processCmd = $this->binDirPath . 'console ' . $this->command->getName() . ' ' . $args;
            $processCmdSuffix = ' > /dev/null 2>/dev/null &';

            if (file_exists($pidFilePath)) {
                if ($this->taskConfig->isWithOverlapping()) {
                    unlink($pidFilePath);
                } else {
                    $pid = intval(file_get_contents($pidFilePath));
                    $result = shell_exec("ps -fp {$pid}");
                    if(strpos($result, $processCmd) !== false) {
                        return;
                    }
                }
            }

            if (!$this->taskConfig->isWithOverlapping()) {
                file_put_contents($pidFilePath, '');
                $processCmdSuffix .= ' echo $! >> ' . $pidFilePath;
            }
            
            shell_exec($processCmd . $processCmdSuffix);
        }
    }

    /**
     * @return string
     */
    private function getPidFileDir()
    {
        return $this->binDirPath . '..'
            . DIRECTORY_SEPARATOR . 'var'
            . DIRECTORY_SEPARATOR . 'comparon_scheduling'
            . DIRECTORY_SEPARATOR
        ;
    }

    /**
     * @param string $dirPath
     * @throws \Exception
     */
    private function createDir($dirPath)
    {
        if (!file_exists($dirPath) && !mkdir($dirPath)) {
            throw new \Exception("Could not create directory {$dirPath}");
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
        // TODO: implement logging
        return false;
    }
}

