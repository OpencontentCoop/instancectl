<?php

namespace Opencontent\InstanceCtl\Command;

use Symfony\Component\Console\Command\Command;
use Opencontent\InstanceCtl\Tools\ConfigHandler;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;

class ConfigCommand extends Command
{
    /**
     * @var ConfigHandler
     */
    private $handler;

    /**
     * @var SymfonyStyle
     */
    private $io;

    protected function configure()
    {
        $this
            ->setName('config')
            ->setDescription('Manage instancectl config')
            ->addOption(
                'dump', 'd',
                InputOption::VALUE_NONE, 'Dump current config'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->handler = new ConfigHandler();
        $this->io = new SymfonyStyle($input, $output);

        if ($input->getOption('dump') && $this->handler->exists()) {
            $this->handler->dump();
        } else {
            $this->runManualConfig($input, $output);
        }
    }

    private function runManualConfig(InputInterface $input, OutputInterface $output)
    {
        if (OutputInterface::VERBOSITY_NORMAL === $output->getVerbosity()) {
            $output->writeln('<comment>Manual instancectl config</comment>');
        }

        $config = ConfigHandler::current();
        foreach ($config->toArray() as $key => $value) {
            if ($key == 'instancectl_gateway') {
                $newValue = $this->io->choice(
                    $key,
                    array(ConfigHandler::GATEWAY_FILESYSTEM, ConfigHandler::GATEWAY_REMOTE),
                    ConfigHandler::GATEWAY_FILESYSTEM
                );
            } else {
                $newValue = $this->io->ask($key, $value);
            }

            $config->{$key} = $newValue;
        }

        $this->handler->store($config);
        $this->handler->dump();
    }

}
