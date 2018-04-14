<?php

namespace Opencontent\InstanceCtl\Command;

use Opencontent\InstanceCtl\Installers\InstallerLoader;
use Opencontent\InstanceCtl\Tools\Instance;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Opencontent\InstanceCtl\Tools\InstancesHandler;
use Symfony\Component\Console\Style\SymfonyStyle;

class InstallCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('install')
            ->setDescription('Install component in instance')
            ->addArgument('instance', InputArgument::REQUIRED)
            ->addArgument('component', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $instance = InstancesHandler::load($input->getArgument('instance'));
        if (!$instance instanceof Instance){
            throw new \Exception("Instance $instance not found");
        }

        $component = $input->getArgument('component');
        $installer = InstallerLoader::load($component);
        $installer->setOutput($output);
        $installer->setInstance($instance);
        $installer->install();
    }

}
