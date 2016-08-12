<?php

namespace Comparon\SchedulingBundle\Command;

use Comparon\SchedulingBundle\Annotation\TaskSchedule;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\Common\Annotations\Reader;

/**
 * @TaskSchedule("4711")
 */
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
        $commands = $this->scanCommands();
        foreach ($commands as $command) {
            $output->writeln($command->getName());
        }
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
