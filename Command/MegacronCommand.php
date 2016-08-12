<?php

namespace Comparon\SchedulingBundle\Command;

use Comparon\SchedulingBundle\Model\TaskConfiguration;
use Comparon\SchedulingBundle\Model\TaskInterface;
use Cron\CronExpression;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\Common\Annotations\Reader;

class MegacronCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('Megacron')
            ->setDescription('...')
            ->addArgument('argument', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $now = new \DateTime('now');
        foreach ($this->getApplication()->all() as $command) {
            if ($command instanceof TaskInterface) {
                $configs = $command->getTaskConfigurations();
                foreach ($configs as $config) {
                    if ($this->isDue($config, $now)) {
                        $this->processTask($command, $config);
                    }
                }
            }
        }
    }

    private function processTask($command, $config) {
        
    }

    private function isDue(TaskConfiguration $taskConfiguration, \DateTime $now)
    {
        $expression = $taskConfiguration->getCronExpression();
        if (CronExpression::isValidExpression($expression)) {
            $cron = CronExpression::factory($expression);
            return $cron->isDue($now);
        }
        // TODO: Log
        return false;
    }
}
