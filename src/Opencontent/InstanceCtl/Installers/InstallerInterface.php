<?php

namespace Opencontent\InstanceCtl\Installers;

use Opencontent\InstanceCtl\Tools\Instance;
use Symfony\Component\Console\Output\OutputInterface;

interface InstallerInterface
{
    /**
     * @return array
     */
    public function getActions();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return void
     */
    public function install();

    /**
     * @return OutputInterface
     */
    public function getOutput();

    /**
     * @param OutputInterface $output
     *
     * @return void
     */
    public function setOutput($output);

    /**
     * @return Instance
     */
    public function getInstance();

    /**
     * @param Instance $instance
     */
    public function setInstance($instance);
}
