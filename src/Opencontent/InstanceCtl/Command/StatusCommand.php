<?php

namespace Opencontent\InstanceCtl\Command;

use Symfony\Component\Console\Command\Command;
use Opencontent\InstanceCtl\Tools\InstancesHandler;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\VarDumper\VarDumper;
use Opencontent\InstanceCtl\Tools\ConfigHandler;

class StatusCommand extends Command
{
    /**
     * @var SymfonyStyle
     */
    private $io;

    protected function configure()
    {
        $this
            ->setName('status')
            ->setDescription('instances.yml status');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!ConfigHandler::isConfigured()){
            throw new \Exception("Run config command before...");
        }
        $this->io = new SymfonyStyle($input, $output);
        $status = InstancesHandler::getStatus();
        $output->writeln("Instance count: " . $status['instances_count']);
        $output->writeln("Last update: " . $status['last_update']);
    }
}
