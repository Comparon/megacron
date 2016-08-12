<?php

namespace Comparon\SchedulingBundle\Facade;

use Comparon\SchedulingBundle\Model\TaskConfiguration;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class TaskProcessorFacade
{
    public static function process(
        OutputInterface $output,
        ContainerAwareCommand $command,
        TaskConfiguration $config
    ) {
        $input = new ArgvInput($config->getParameters());
        try {
            $output->writeln($command->getName());
            $returnCode = $command->execute($input, $output);
        } catch (\Exception $e) {
            $returnCode = -1;
            //TODO LOG
        }
        return $returnCode;
    }
}