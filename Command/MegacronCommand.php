<?php

namespace Comparon\SchedulingBundle\Command;

use Comparon\SchedulingBundle\Annotation\TaskSchedule;
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
        $commands = $this->getScheduledTasks();
        foreach ($commands as $command) {
            $output->writeln($command->getName());
        }
    }

    /**
     * @return array
     */
    private function getScheduledTasks()
    {
        $tasks = [];
        /** @var Reader */
        $annotationReader = $this->getContainer()->get('annotation_reader');
        $now = new \DateTime('now');
        foreach ($this->getApplication()->all() as $command) {
            $reflectionClass = new \ReflectionClass($command);
            foreach ($annotationReader->getClassAnnotations($reflectionClass) as $annotation) {
                if ($annotation instanceof TaskSchedule && $this->isScheduled($annotation, $now)) {
                    $tasks[] = $command;
                }
            }
        }
        return $tasks;
    }

    private function isScheduled($annotation, $now)
    {
        $expression = $annotation->getCronExpression();
        if (CronExpression::isValidExpression($expression)) {
            $cron = CronExpression::factory($expression);
            return $cron->isDue($now);
        }
        // TODO: Log
        return false;
    }
}
