<?php

namespace Opencontent\InstanceCtl\Command;

use Opencontent\InstanceCtl\Tools\Instance;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Opencontent\InstanceCtl\Tools\InstancesHandler;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\VarDumper\VarDumper;

class ReadCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('get')
            ->setDescription('Read instance')
            ->addArgument('instance', InputArgument::OPTIONAL)
            ->addArgument('property', InputArgument::OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $identifier = $input->getArgument('instance');
        if (!$identifier){
            $instances = InstancesHandler::load();
            $identifier = $io->choice(
                'Select instance',
                array_keys($instances)
            );
        }
        $instance = InstancesHandler::load($identifier);

        $property = $input->getArgument('property');
        if (!$property && $instance instanceof Instance){
            $properties = array_keys($instance->jsonSerialize());
            $properties[] = '*';
            $property = $io->choice(
                'Select property',
                $properties
            );

            if ($property == '*'){
                $property = null;
            }
        }

        if ($property){
            $data = $instance->jsonSerialize();
            VarDumper::dump($data[$property]);
        }else{
            InstancesHandler::dump($instance);
        }
    }
}
