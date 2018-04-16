<?php

namespace Opencontent\InstanceCtl\Command;

use Opencontent\InstanceCtl\Tools\Instance;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Opencontent\InstanceCtl\Tools\InstancesHandler;
use Opencontent\InstanceCtl\Tools\ConfigHandler;

class RunScriptCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('run')
            ->setDescription('Run script in instance selection')
            ->addArgument('script', InputArgument::REQUIRED, 'Script to execute')
            ->addArgument('script_parameters', InputArgument::OPTIONAL, 'Script arguments and parameters')
            ->addArgument('instance', InputArgument::OPTIONAL)
            ->addOption('filter', 'f', InputOption::VALUE_OPTIONAL, 'Filter instances by tag');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!ConfigHandler::isConfigured()){
            throw new \Exception("Run config command before...");
        }
        $instances = InstancesHandler::filter($input->getOption('filter'));

        $identifier = $input->getArgument('instance');
        if ($identifier){
            $instances = [InstancesHandler::load($identifier)];
        }

        $script = $input->getArgument('script');
        $scriptParams = $input->getArgument('script_parameters');

        foreach ($instances as $instance) {

            $scriptToRun = "php $script -s " . $instance->getBackendSiteAccess() . ' ' . $scriptParams;

            print_r($scriptToRun);

            $child = popen($scriptToRun, 'r');

            $response = stream_get_contents($child);

            //print_r($response);
        }
    }

}
