<?php

namespace Opencontent\InstanceCtl\Command;

use Opencontent\InstanceCtl\Tools\ConfigHandler;
use Opencontent\InstanceCtl\Tools\Instance;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Opencontent\InstanceCtl\Tools\InstancesHandler;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\VarDumper\VarDumper;
use Symfony\Component\Console\Input\InputOption;

class ReadCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('get')
            ->setDescription('Read instance')
            ->addArgument('instance', InputArgument::OPTIONAL)
            ->addArgument('property', InputArgument::OPTIONAL)
            ->addOption('filter', 'f', InputOption::VALUE_OPTIONAL, 'Filter instances by tag');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!ConfigHandler::isConfigured()){
            throw new \Exception("Run config command before...");
        }
        $io = new SymfonyStyle($input, $output);

        $identifier = $input->getArgument('instance');
        if (!$identifier){
            $instances = InstancesHandler::filter($input->getOption('filter'));
            $identifier = $io->choice(
                'Select instance',
                array_keys($instances)
            );
        }
        $instance = InstancesHandler::load($identifier);

        $property = $input->getArgument('property');
        if (!$property && $instance instanceof Instance){
            $properties = array_keys($instance->jsonSerialize());
            $properties[] = '.';
            $property = $io->choice(
                'Select property',
                $properties
            );
        }

        if ($property == '.'){
            $property = false;
        }

        if ($property){
            $data = $instance->jsonSerialize();
            if(!isset($data[$property])){
                throw new \Exception("Property $property not found. Available properties: " . implode(', ', array_keys($data)));
            }
            VarDumper::dump($data[$property]);
        }else{
            InstancesHandler::dump($instance);
        }
    }
}
