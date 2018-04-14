<?php

namespace Opencontent\InstanceCtl\Installers;

use Opencontent\InstanceCtl\Tools\Instance;
use Opencontent\InstanceCtl\Tools\LegacyKernel;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

abstract class AbstractInstaller implements InstallerInterface
{
    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var Instance
     */
    private $instance;

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
     * @return Instance
     */
    public function getInstance()
    {
        return $this->instance;
    }

    /**
     * @param Instance $instance
     */
    public function setInstance($instance)
    {
        $this->instance = $instance;
    }

    public function install()
    {
        foreach ($this->getActions() as $action => $params) {
            if ($action == 'ini') {
                $this->installIni($params);
            }
        }
    }

    protected function installIni($params)
    {
        $this->getOutput()->writeln("Modify ini");
        $params = $this->replacePlaceholders($params);
        $fileSystem = new Filesystem();

        LegacyKernel::call(function () use ($params, $fileSystem) {

            foreach ($params as $modification) {
                $path = \eZSiteAccess::findPathToSiteAccess($modification['siteaccess']);
                if (!$path){
                    $path = 'settings/siteaccess/' . $modification['siteaccess'];
                    $fileSystem->mkdir($path);
                }

                foreach ($modification['items'] as $item) {
                    $fileIni = $item['file'];
                    if (strpos($fileIni, '.append.php') === false){
                        $fileIni .= '.append.php';
                    }

                    if (!file_exists($path . '/' . $fileIni)){
                        $fileSystem->touch($path . '/' . $fileIni);
                    }

                    $append = isset($item['mode']) && $item['mode'] == 'append';
                    $ini = \eZINI::instance($fileIni, $path, null, false, false, true, true);
                    if (!$ini->exists($ini->filename(), $ini->rootDir())){
                        throw new \Exception("Fail creating $path/$fileIni");
                    }
                    foreach($item['values'] as $blockName => $value){
                        foreach($value as $varName => $newValue) {
                            if (
                                is_array($newValue)
                                && $ini->hasVariable($blockName, $varName)
                                && $append
                            ){
                                $currentValue = $ini->variable($blockName, $varName);
                                $newValue = array_unique(array_merge($currentValue, $newValue));
                            }
                            $ini->setVariable($blockName, $varName, $newValue);
                        }
                    }
                    if (!$ini->save()){
                        throw new \Exception("Can not save $fileIni");
                    }
                }
            }
        }, getcwd());

    }

    protected function replacePlaceholders($params)
    {
        return $this->replaceArray($params);
    }

    protected function replaceArray($params)
    {
        $parsedParams = array();
        foreach($params as $index => $value)
        {
            $parsedIndex = $this->replaceString($index);
            if (is_array($value)) {
                $parsedValue = $this->replaceArray($value);
            }else{
                $parsedValue = $this->replaceString($value);
            }
            $parsedParams[$parsedIndex] = $parsedValue;
        }

        return $parsedParams;
    }

    protected function replaceString($string)
    {
        $string = str_replace('%identifier%', $this->instance->getIdentifier(), $string);
        $string = str_replace('%solr_host%', $this->instance->getSolrHost(), $string);

        return $string;
    }

}
