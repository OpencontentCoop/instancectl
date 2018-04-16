<?php

namespace Opencontent\InstanceCtl\Tools;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Opencontent\InstanceCtl\Tools\SiteAccessIdentifierParser\SiteAccessIdentifierUnderscoreParser;
use Opencontent\InstanceCtl\Tools\Tagger\OpenpaTagger;
use Opencontent\InstanceCtl\Ez\eZINI;

class InstanceParser
{
    use IniLoaderTrait;

    /**
     * @var Instance[]
     */
    private $instances;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var SiteAccessIdentifierParserInterface
     */
    private $identifierParser;

    /**
     * @var TaggerInterface;
     */
    private $tagger;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
        $this->identifierParser = new SiteAccessIdentifierUnderscoreParser();
        $this->tagger = new OpenpaTagger();
    }

    public function getInstances()
    {
        if ($this->instances === null) {
            $siteAccessList = $this->getIni('site.ini')->variable('SiteAccessSettings', 'AvailableSiteAccessList');

            $this->output->writeln("Parsing settings to generate instancectl cache...");
            $progress = new ProgressBar($this->output, count($siteAccessList));
            $progress->setFormatDefinition('custom', ' %current%/%max% -- %message%');
            $progress->setFormat('custom');
            $progress->start();

            foreach ($siteAccessList as $siteAccess) {
                $progress->setMessage($siteAccess);
                $progress->advance();
                $siteAccessData = null;
                $identifier = $this->identifierParser->parse($siteAccess);

                if (!isset($this->instances[$identifier])) {
                    $instance = new Instance();
                    $instance->setIdentifier($identifier);
                    $this->instances[$identifier] = $instance;
                }

                /** @var eZINI $siteIni */
                $siteIni = $this->getIni('site.ini', $siteAccess);

                /** @var eZINI $solrIni */
                $solrIni = $this->getIni('solr.ini', $siteAccess);

                $designList = $siteIni->variable('DesignSettings', 'AdditionalSiteDesignList');
                array_unshift($designList, $siteIni->variable('DesignSettings', 'SiteDesign'));

                $this->instances[$identifier]
                    ->addSiteAccess($siteAccess, eZINI::getBaseRoot() . 'setting/siteaccess/' . $siteAccess)
                    ->addVarDir($siteAccess, $siteIni->variable('FileSettings', 'VarDir'))
                    ->addDbHost($siteAccess, $siteIni->variable('DatabaseSettings', 'Server'))
                    ->addDbType($siteAccess,
                        $siteIni->variable('DatabaseSettings', 'DatabaseImplementation'))
                    ->addDbUser($siteAccess, $siteIni->variable('DatabaseSettings', 'User'))
                    ->addDb($siteAccess, $siteIni->variable('DatabaseSettings', 'Database'))
                    ->addDbPort($siteAccess, $siteIni->variable('DatabaseSettings', 'Port'))
                    ->addName($siteAccess, $siteIni->variable('SiteSettings', 'SiteName'))
                    ->addDesignList($siteAccess, $designList)
                    ->addSolrHost($siteAccess, $solrIni->variable('SolrBase', 'SearchServerURI'));

                $cacheDir = $siteIni->variable('FileSettings', 'CacheDir');
                if (empty($cacheDir)){
                    $cacheDir = 'cache';
                }
                $this->instances[$identifier]->addCacheDir($siteAccess, $cacheDir);
                $storageDir = $siteIni->variable('FileSettings', 'StorageDir');
                if (empty($storageDir)){
                    $storageDir = 'storage';
                }
                $this->instances[$identifier]->addStorageDir($siteAccess, $storageDir);
            }
            $progress->finish();

            $this->output->writeln("Find main domain and main siteaccess...");
            $hostUriMatchMapItems = $this->getIni('site.ini')->variable('SiteAccessSettings', 'HostUriMatchMapItems');
            foreach ($hostUriMatchMapItems as $hostUriMatchMapItem) {
                list($host, $uri, $siteAccess) = explode(';', $hostUriMatchMapItem);
                $identifier = $this->identifierParser->parse($siteAccess);
                if (isset($this->instances[$identifier])) {
                    $this->instances[$identifier]->addHostUriMatchList($host, $uri, $siteAccess);
                }
            }

            foreach ($this->instances as $identifier => $instance) {
                $this->decorateInstance($instance);
                $this->tagger->tag($instance);
            }
        }

        return $this->instances;
    }

    private function decorateInstance(Instance $instance)
    {
        foreach ($instance->getSiteAccessList() as $siteAccess => $path) {
            if ($siteAccess == $instance->getIdentifier() . '_backend') {
                $instance->setBackendSiteAccess($siteAccess);
            }
            if ($siteAccess == $instance->getIdentifier() . '_frontend') {
                $instance->setMainSiteaccess($siteAccess);
            }
        }

        foreach ($instance->getNameList() as $siteAccess => $name){
            if ($siteAccess == $instance->getIdentifier() . '_frontend') {
                $instance->setName($name);
            }
        }

        foreach ($instance->getHostUriMatchList() as $siteAccess => $values) {
            if ($siteAccess == $instance->getMainSiteaccess()) {
                foreach ($values as $host => $uri) {
                    if (empty($uri)) {
                        $instance->setMainDomain($host);
                        break;
                    }
                }
            }
        }

        $data = array_unique(array_values($instance->getVarDirList()));
        if (isset($data[0])) {
            $instance->setVarDir($data[0]);
        }

        $data = array_unique(array_values($instance->getCacheDirList()));
        $cacheDir = isset($data[0]) ? $data[0] : null;
        if ($cacheDir && $cacheDir[0] != '/') {
            $cacheDir = $instance->getVarDir() . '/' . $cacheDir;
        }
        $instance->setCacheDir($cacheDir);

        $data = array_unique(array_values($instance->getStorageDirList()));
        $storageDir = isset($data[0]) ? $data[0] : null;
        if ($storageDir && $storageDir[0] != '/') {
            $storageDir = $instance->getVarDir() . '/' . $storageDir;
        }
        $instance->setStorageDir($storageDir);

        $data = array_unique(array_values($instance->getDbHostList()));
        if (isset($data[0])) {
            $instance->setDbHost($data[0]);
        }

        $data = array_unique(array_values($instance->getDbPortList()));
        if (isset($data[0])) {
            $instance->setDbPort($data[0]);
        }

        $data = array_unique(array_values($instance->getDbTypeList()));
        if (isset($data[0])) {
            $instance->setDbType($data[0]);
        }

        $data = array_unique(array_values($instance->getDbList()));
        if (isset($data[0])) {
            $instance->setDb($data[0]);
        }

        $data = array_unique(array_values($instance->getDbUserList()));
        if (isset($data[0])) {
            $instance->setDbUser($data[0]);
        }

        $data = array_unique(array_values($instance->getSolrHostList()));
        if (isset($data[0])) {
            $instance->setSolrHost($data[0]);
        }

        if ($instance->getMainDomain() && ConfigHandler::current()->web_server_type == 'nginx') {
            $nginx = new NginxParser();
            $result = $nginx->find($instance->getMainDomain())->getResults();
            foreach ($result as $item) {
                $instance->setVirtualHost($item['path']);
                foreach ($item['aliases'] as $domain) {
                    $instance->addDomain($domain);
                }
            }
        }


    }
}
