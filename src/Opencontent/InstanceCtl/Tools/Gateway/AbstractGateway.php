<?php

namespace Opencontent\InstanceCtl\Tools\Gateway;

use Opencontent\InstanceCtl\Tools\InstanceExporter;
use Symfony\Component\Console\Output\OutputInterface;
use Opencontent\InstanceCtl\Tools\Instance;

abstract class AbstractGateway
{
    /**
     * @var OutputInterface
     */
    private $output;

    private $filename;

    private $exporter;

    public function __construct()
    {
        $this->exporter = new InstanceExporter();
    }

    /**
     * @return OutputInterface
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @param OutputInterface $output
     */
    public function setOutput($output)
    {
        $this->output = $output;
    }

    /**
     * @return mixed
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @param mixed $filename
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
    }

    /**
     * @return InstanceExporter
     */
    public function getExporter()
    {
        return $this->exporter;
    }

    /**
     * @param InstanceExporter $exporter
     */
    public function setExporter($exporter)
    {
        $this->exporter = $exporter;
    }

    /**
     * @param Instance[] $instances
     *
     * @throws \Exception
     */
    abstract public function store(array $instances);

}
