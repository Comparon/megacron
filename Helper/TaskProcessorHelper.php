<?php

namespace Comparon\MegacronBundle\Helper;

use Comparon\MegacronBundle\Model\TaskConfiguration;
use Cron\CronExpression;
use Symfony\Component\Console\Command\Command;

class TaskProcessorHelper
{
    /** @var string */
    private $binDirPath;

    /** @var Command */
    private $command;

    /** @var TaskConfiguration */
    private $taskConfig;

    /** @var string */
    private string $phpBinaryPath;

    /**
     * @param string $binDirPath
     * @param Command $command
     * @param TaskConfiguration $taskConfig
     * @param string $phpBinaryPath
     */
    public function __construct(string $binDirPath, Command $command, TaskConfiguration $taskConfig, string $phpBinaryPath = '')
    {
        $this->binDirPath = $binDirPath;
        $this->command = $command;
        $this->taskConfig = $taskConfig;
        $this->phpBinaryPath = $phpBinaryPath;
    }

    /**
     * @throws \Exception
     */
    public function process(): void
    {
        $pidFileDir = $this->getPidFileDir();
        $this->createDir($pidFileDir);

        $processHash = sha1($this->command->getName() . $this->taskConfig->getCronExpression());
        $pidFilePath = $pidFileDir . $processHash . '.pid';

        if ($this->isDue()) {
            $processCmd = $this->binDirPath . 'console ' . $this->command->getName();
            $processCmdSuffix = ' > /dev/null 2>/dev/null &';

            if (count($this->taskConfig->getParameters()) > 0) {
                $processCmd .= ' ' . implode(' ', $this->taskConfig->getParameters());
            }

            if (file_exists($pidFilePath)) {
                if ($this->taskConfig->isWithOverlapping()) {
                    unlink($pidFilePath);
                } else {
                    $pid = intval(file_get_contents($pidFilePath));
                    $result = shell_exec("ps -fp {$pid}");
                    if (strpos($result, $processCmd) !== false) {
                        return;
                    }
                }
            }

            if (!$this->taskConfig->isWithOverlapping()) {
                file_put_contents($pidFilePath, '');
                $processCmdSuffix .= ' echo $! >> ' . $pidFilePath;
            }

            shell_exec($this->phpBinaryPath.' '.$processCmd . $processCmdSuffix);
        }
    }

    /**
     * @return string
     */
    private function getPidFileDir()
    {
        return $this->binDirPath . '..'
            . DIRECTORY_SEPARATOR . 'var'
            . DIRECTORY_SEPARATOR . 'megacron'
            . DIRECTORY_SEPARATOR;
    }

    /**
     * @param string $dirPath
     * @throws \Exception
     */
    private function createDir(string $dirPath): void
    {
        if (!file_exists($dirPath) && !mkdir($dirPath)) {
            throw new \Exception("Could not create directory {$dirPath}");
        }
    }

    /**
     * @return bool
     */
    private function isDue(): bool
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

