<?php

namespace Opencontent\InstanceCtl\Command;

use Opencontent\InstanceCtl\Tools\Instance;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Opencontent\InstanceCtl\Tools\InstancesHandler;
use Symfony\Component\Console\Style\SymfonyStyle;

class RunScriptCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('cron')
            ->setDescription('Run runcronjobs script in instance selection')
            ->addOption('filter', 'f', InputOption::VALUE_OPTIONAL, 'Instance filter')
            ->addArgument('script', InputArgument::OPTIONAL, 'Script to execute')
            ->addArgument('cron', InputArgument::OPTIONAL, 'Cron to execute');;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $instances = InstancesHandler::filter($input->getOption('filter'));

        $instance = InstancesHandler::load('trento');

        $script = $input->getArgument('script');
        if (!$script){
            $script = 'runcronjobs.php';
        }
        $cronPart = $input->getArgument('cron');

        $scriptToRun = "php $script -s " . $instance->getBackendSiteAccess() . ' '  . $cronPart;

        print_r($scriptToRun);

        $child = popen($scriptToRun, 'r');

        $response = stream_get_contents($child);

        print_r($response);
    }

}
