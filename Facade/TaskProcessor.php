<?php

namespace Comparon\SchedulingBundle\Helper;

use Comparon\SchedulingBundle\Model\TaskConfiguration;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class TaskProcessorFacade
{

    public static function process(ContainerAwareCommand $command, TaskConfiguration $config)
    {
        $input = new ArgvInput($config->getParameters());
        $output = new MemoryWriter();
        try {
            $returnCode = $command->execute($input, $output);
        } catch (\Exception $e) {
            $returnCode = -1;
            //TODO LOG
        }
        return $returnCode;
    }
}