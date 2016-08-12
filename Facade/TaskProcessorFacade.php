<?php

namespace Comparon\SchedulingBundle\Facade;

use Comparon\SchedulingBundle\Model\TaskConfiguration;
use Cron\CronExpression;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Process\Process;

class TaskProcessorFacade
{
    public static function process(ContainerAwareCommand $command, TaskConfiguration $config)
    {
        if (self::isDue($config)) {
            $args = implode(' ', $config->getParameters());
            $process = new Process('./console ' . $command->getName() . ' ' . $args);
            $process->run();
        }
    }

    private static function isDue(TaskConfiguration $taskConfiguration)
    {
        $expression = $taskConfiguration->getCronExpression();
        if (CronExpression::isValidExpression($expression)) {
            $cron = CronExpression::factory($expression);
            return $cron->isDue(new \DateTime('now'));
        }
        // TODO: Log
        return false;
    }
}
