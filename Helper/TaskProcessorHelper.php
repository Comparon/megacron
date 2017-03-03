<?php

namespace Comparon\MegacronBundle\Helper;

use Comparon\MegacronBundle\Entity\MegaCronHistory;
use Comparon\MegacronBundle\Model\TaskConfiguration;
use Cron\CronExpression;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;

use Symfony\Component\Process\Process;

class TaskProcessorHelper
{
    /** @var string */
    private $binDirPath;

    /** @var Command */
    private $command;

    /** @var TaskConfiguration */
    private $taskConfig;

    /** @var EntityManagerInterface | null */
    private $em;

    /** @var bool */
    private $persistHistory;

    /**
     * @param string $binDirPath
     * @param Command $command
     * @param TaskConfiguration $taskConfig
     */
    public function __construct($binDirPath, Command $command, TaskConfiguration $taskConfig, EntityManagerInterface $em = null)
    {
        $this->binDirPath = $binDirPath;
        $this->command = $command;
        $this->taskConfig = $taskConfig;
        $this->em = $em;
        $this->persistHistory = ($em instanceof EntityManagerInterface) && ($this->taskConfig->isPersistHistory());
    }

    public function process()
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

            $megacronHistoryEntry = $this->setMegaCronHistoryStarted();

            if (file_exists($pidFilePath)) {
                if ($this->taskConfig->isWithOverlapping()) {
                    unlink($pidFilePath);
                } else {
                    $pid = intval(file_get_contents($pidFilePath));
                    $result = shell_exec("ps -fp {$pid}");
                    if (strpos($result, $processCmd) !== false) {
                        $this->setMegaCronHistoryStopped($megacronHistoryEntry);
                        return;
                    }
                }
            }

            if (!$this->taskConfig->isWithOverlapping()) {
                file_put_contents($pidFilePath, '');
                $processCmdSuffix .= ' echo $! >> ' . $pidFilePath;
            }

            shell_exec($processCmd . $processCmdSuffix);

           $this->setMegaCronHistoryStopped($megacronHistoryEntry);
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

    /**
     * @return MegaCronHistory|null
     */
    private function setMegaCronHistoryStarted(){
        if ($this->persistHistory) {
            $megaCronHistory = new MegaCronHistory();
            $megaCronHistory->setCronJobName($this->command->getName());
            $this->em->persist($megaCronHistory);
            $this->em->flush();
            return $megaCronHistory;
        }
        return null;
    }

    /**
     * @param MegaCronHistory|null $megaCronHistory
     */
    private function setMegaCronHistoryStopped(MegaCronHistory $megaCronHistory = null){
        if (($this->persistHistory) && ($megaCronHistory instanceof MegaCronHistory)) {
            $megaCronHistory->setStopped(new \DateTime());
            $this->em->persist($megaCronHistory);
            $this->em->flush();
        }
    }
}

