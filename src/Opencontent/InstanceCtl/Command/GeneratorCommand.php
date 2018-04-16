<?php

namespace Opencontent\InstanceCtl\Command;

use Opencontent\InstanceCtl\Tools\InstancesHandler;
use Opencontent\InstanceCtl\Tools\InstanceParser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Opencontent\InstanceCtl\Tools\ConfigHandler;

class GeneratorCommand extends Command
{

    protected function configure()
    {
        $this
            ->setName('generate')
            ->setDescription('Parse instance form ini, store cache and generate instances.yml file')
            ->addOption(
                'filename', 'f',
                InputOption::VALUE_REQUIRED, 'Filename'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!ConfigHandler::isConfigured()){
            throw new \Exception("Run config command before...");
        }
        $start = time();

        $parser = new InstanceParser($output);
        $instances = $parser->getInstances();
        InstancesHandler::setCache($instances);

        $gateway = InstancesHandler::getGateway();
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
            //$output->writeln("Time occurred: $durationString");
        }

        $status = InstancesHandler::getStatus();
        $output->writeln("Instance count: " . $status['instances_count']);
    }

}
