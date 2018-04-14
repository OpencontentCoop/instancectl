<?php

namespace Opencontent\InstanceCtl\Tools;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;

class InstanceParser
{
    /**
     * @var \eZINI
     */
    private $globalSiteIni;

    /**
     * @var Instance[]
     */
    private $instances;

    /**
     * @var OutputInterface
     */
    private $output;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * @return \eZINI
     */
    private function getGlobalSiteIni()
    {
        if ($this->globalSiteIni === null) {
            $this->globalSiteIni = LegacyKernel::call(function () {
                return \eZINI::instance('site.ini', "settings/override/", null, false, false, true, true);
            }, getcwd());
        }

        return $this->globalSiteIni;
    }

    public function getInstances()
    {
        if ($this->instances === null) {
            $siteAccessList = LegacyKernel::call(function () {
                return \eZSiteAccess::siteAccessList();
            }, getcwd());

            $this->output->writeln("Parsing settings to generate instancectl cache...");
            $progress = new ProgressBar($this->output, count($siteAccessList));
            $progress->setFormatDefinition('custom', ' %current%/%max% -- %message%');
            $progress->setFormat('custom');
            $progress->start();

            foreach ($siteAccessList as $siteAccess) {
                $progress->advance();
                $progress->setMessage($siteAccess['name']);
                $siteAccessData = null;
                $identifier = SiteAccessNameParser::getInstanceIdentifier($siteAccess['name']);
                try {
                    $siteAccessData = LegacyKernel::call(function () use ($siteAccess) {
                        $path = \eZSiteAccess::findPathToSiteAccess($siteAccess['name']);
                        $tags = array();

                        return array(
                            'path' => $path,
                            'site_ini' => \eZINI::instance('site.ini'),
                            'solr_ini' => \eZINI::instance('solr.ini', $path, null, false, false, true, true),
                            'tags' => \ezpEvent::getInstance()->filter('instancectl/tag', $tags)
                        );
                    }, getcwd(), $siteAccess['name']);
                } catch (\Exception $e) {
                    $this->output->writeln('<error>' . $e->getMessage() . '</error>');
                }

                if ($siteAccessData) {
                    if (!isset( $this->instances[$identifier] )) {
                        $instance = new Instance();
                        $instance->setIdentifier($identifier);
                        $this->instances[$identifier] = $instance;
                    }
                    /** @var \eZINI $siteIni */
                    $siteIni = $siteAccessData['site_ini'];
                    /** @var \eZINI $solrIni */
                    $solrIni = $siteAccessData['solr_ini'];
                    $designList = $siteIni->variable('DesignSettings', 'AdditionalSiteDesignList');
                    array_unshift($designList, $siteIni->variable('DesignSettings', 'SiteDesign'));

                    $this->instances[$identifier]
                        ->addSiteAccess($siteAccess['name'], $siteAccessData['path'])
                        ->addTags($siteAccessData['tags'])
                        ->addCacheDir($siteAccess['name'], $siteIni->variable('FileSettings', 'CacheDir'))
                        ->addVarDir($siteAccess['name'], $siteIni->variable('FileSettings', 'VarDir'))
                        ->addStorageDir($siteAccess['name'], $siteIni->variable('FileSettings', 'StorageDir'))
                        ->addDbHost($siteAccess['name'], $siteIni->variable('DatabaseSettings', 'Server'))
                        ->addDbType($siteAccess['name'],
                            $siteIni->variable('DatabaseSettings', 'DatabaseImplementation'))
                        ->addDbUser($siteAccess['name'], $siteIni->variable('DatabaseSettings', 'User'))
                        ->addDb($siteAccess['name'], $siteIni->variable('DatabaseSettings', 'Database'))
                        ->addDbPort($siteAccess['name'], $siteIni->variable('DatabaseSettings', 'Port'))
                        ->addName($siteAccess['name'], $siteIni->variable('SiteSettings', 'SiteName'))
                        ->addDesignList($siteAccess['name'], $designList)
                        ->addSolrHost($siteAccess['name'], $solrIni->variable('SolrBase', 'SearchServerURI'));
                }
            }
            $progress->finish();

            $this->output->writeln("Find main domain and main siteaccess...");
            $hostUriMatchMapItems = $this->getGlobalSiteIni()->variable('SiteAccessSettings', 'HostUriMatchMapItems');
            foreach ($hostUriMatchMapItems as $hostUriMatchMapItem) {
                list( $host, $uri, $siteAccess ) = explode(';', $hostUriMatchMapItem);
                $identifier = SiteAccessNameParser::getInstanceIdentifier($siteAccess);
                if (isset( $this->instances[$identifier] )){
                    $this->instances[$identifier]->addHostUriMatchList($host, $uri, $siteAccess);
                }
            }

            $this->output->writeln("Find main configuration and server alias...");
            foreach($this->instances as $identifier => $instance){
                $this->decorateInstance($instance);
            }
        }

        return $this->instances;
    }

    private function decorateInstance(Instance $instance)
    {
        foreach ($instance->getSiteAccessList() as $siteAccess => $path) {
            if( $siteAccess == $instance->getIdentifier() . '_backend'){
                $instance->setBackendSiteAccess($siteAccess);
            }
            if( $siteAccess == $instance->getIdentifier() . '_frontend'){
                $instance->setMainSiteaccess($siteAccess);
            }
        }

        foreach($instance->getHostUriMatchList() as $siteAccess => $values){
            if ($siteAccess == $instance->getMainSiteaccess()){
                foreach($values as $host => $uri){
                    if (empty($uri)){
                        $instance->setMainDomain($host);
                        break;
                    }
                }
            }
        }

        $data = array_unique(array_values($instance->getVarDirList()));
        if(isset($data[0])){
            $instance->setVarDir($data[0]);
        }

        $data = array_unique(array_values($instance->getCacheDirList()));
        $cacheDir = isset($data[0]) ? $data[0] : null;
        if ($cacheDir && $cacheDir[0] != '/'){
            $cacheDir = $instance->getVarDir() . '/' . $cacheDir;
        }
        $instance->setCacheDir($cacheDir);

        $data = array_unique(array_values($instance->getStorageDirList()));
        $storageDir = isset($data[0]) ? $data[0] : null;
        if ($storageDir && $storageDir[0] != '/'){
            $storageDir = $instance->getVarDir() . '/' . $storageDir;
        }
        $instance->setStorageDir($storageDir);

        $data = array_unique(array_values($instance->getDbHostList()));
        if (isset($data[0])){
            $instance->setDbHost($data[0]);
        }

        $data = array_unique(array_values($instance->getDbPortList()));
        if (isset($data[0])){
            $instance->setDbPort($data[0]);
        }

        $data = array_unique(array_values($instance->getDbTypeList()));
        if (isset($data[0])){
            $instance->setDbType($data[0]);
        }

        $data = array_unique(array_values($instance->getDbList()));
        if (isset($data[0])){
            $instance->setDb($data[0]);
        }

        $data = array_unique(array_values($instance->getDbUserList()));
        if (isset($data[0])){
            $instance->setDbUser($data[0]);
        }

        $data = array_unique(array_values($instance->getSolrHostList()));
        if (isset($data[0])){
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
