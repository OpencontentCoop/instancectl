<?php

namespace Opencontent\InstanceCtl\Command;

use Opencontent\InstanceCtl\Tools\InstancesHandler;
use Opencontent\InstanceCtl\Tools\InstanceParser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;


class GeneratorCommand extends Command
{

    protected function configure()
    {
        $this
            ->setName('generate')
            ->setDescription('Parse instance form ini, store cache and generate instances.yml file')
            ->addOption(
                'clear-cache', 'c',
                InputOption::VALUE_NONE, 'Clear instancectl cache'
            )
            ->addOption(
                'filename', 'f',
                InputOption::VALUE_REQUIRED, 'Filename'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $start = time();

        if ($input->getOption('clear-cache') || !InstancesHandler::hasCache()) {
            $parser = new InstanceParser($output);
            $instances = $parser->getInstances();
            InstancesHandler::setCache($instances);
        } else {
            $instances = InstancesHandler::getCache();
        }

        $output->writeln("Store data");
        $gateway = InstancesHandler::getGateway($instances);
        $gateway->setFilename($input->getOption('filename'));
        $gateway->setOutput($output);
        $gateway->store($instances);

        $duration = time() - $start;
        if ($duration > 0) {
            if ($duration > 60) {
                $minutes = floor($duration / 60);
                $durationString = "~$minutes minutes";
            } elseif ($duration == 1) {
                $durationString = "$duration second";
            } else {
                $durationString = "$duration seconds";
            }
            $output->writeln("Time occurred: $durationString");
        }
    }

}
