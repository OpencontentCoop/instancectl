<?php

namespace Opencontent\InstanceCtl\Command;

use Symfony\Component\Console\Command\Command;
use Opencontent\InstanceCtl\Tools\ConfigHandler;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CleanCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('clean')
            ->setDescription('Clean instancectl file');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $cacheFilepath = getcwd() . '/' . ConfigHandler::current()->cache_filename;
        if (file_exists($cacheFilepath)) {
            $output->writeln("Remove cache file: " . $cacheFilepath);
            unlink($cacheFilepath);
        }

        $instanceFilepath = getcwd() . '/' . ConfigHandler::current()->instances_filename;
        if (file_exists($instanceFilepath)) {
            $output->writeln("Remove instances file: " . $instanceFilepath);
            unlink($instanceFilepath);
        }

        $configFilepath = (new ConfigHandler())->getCurrentFilepath();
        if (file_exists($configFilepath)) {
            $output->writeln("Remove config file: " . $configFilepath);
            unlink($configFilepath);
        }
    }
}
