<?php

namespace Comparon\SchedulingBundle\Command;

use Comparon\SchedulingBundle\Annotation\TaskSchedule;
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
        $argument = $input->getArgument('argument');
        if ($input->getOption('option')) {
            // ...
        }

        $output->writeln('Command result.');
    }

    /**
     * @return array
     */
    private function scanCommands()
    {
        /** @var Reader */
        $annotationReader = $this->getContainer()->get('annotation_reader');
        $commands = [];
        foreach($this->getApplication()->all() as $command)
        {
            $reflectionClass = new \ReflectionClass($command);
            foreach($annotationReader->getClassAnnotations($reflectionClass) as $annotation)
            {
                if($annotation instanceof TaskSchedule)
                {
                    $commands[] = $command;
                }
            }
        }
        return $commands;
    }
}
