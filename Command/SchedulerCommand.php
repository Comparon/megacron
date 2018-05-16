<?php

namespace Comparon\MegacronBundle\Command;

use Comparon\MegacronBundle\Helper\TaskProcessorHelper;
use Comparon\MegacronBundle\Model\TaskInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SchedulerCommand extends Command
{
    /** @var string */
    private $projectDir;

    public function __construct(string $projectDir)
    {
        parent::__construct();
        $this->projectDir = $projectDir;
    }

    protected function configure(): void
    {
        $this
            ->setName('comparon:scheduler:run')
            ->setDescription('...')
            ->addArgument('argument', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        foreach ($this->getApplication()->all() as $command) {
            if ($command instanceof TaskInterface) {
                $configs = $command->getTaskConfigurations();
                foreach ($configs as $config) {
                    (new TaskProcessorHelper($this->getBinDirPath(), $command, $config))->process();
                }
            }
        }
    }

    private function getBinDirPath(): string
    {
        return $this->projectDir
            . DIRECTORY_SEPARATOR  . 'bin'
            . DIRECTORY_SEPARATOR
        ;
    }
}
