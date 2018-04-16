<?php

namespace Opencontent\InstanceCtl\Tools;


class Instance
{
    private $identifier;

    private $siteAccessList = array();

    private $hostUriMatchList = array();

    private $mainDomain;

    private $domains;

    private $virtualHost;

    private $mainSiteaccess;

    private $tags = array();

    private $nameList;

    private $varDirList;

    private $cacheDirList;

    private $storageDirList;

    private $dbList;

    private $dbTypeList;

    private $dbHostList;

    private $dbPortList;

    private $dbUserList;

    private $solrHostList;

    private $designListList;

    private $name;

    private $varDir;

    private $cacheDir;

    private $storageDir;

    private $backendSiteAccess;

    private $db;

    private $dbType;

    private $dbHost;

    private $dbPort;

    private $dbUser;

    private $solrHost;

    /**
     * @return mixed
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @param mixed $identifier
     *
     * @return Instance
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * @return array
     */
    public function getSiteAccessList()
    {
        return $this->siteAccessList;
    }

    /**
     * @param $siteAccess
     * @param $siteAccessPath
     *
     * @return $this
     */
    public function addSiteAccess($siteAccess, $siteAccessPath)
    {
        $this->siteAccessList[$siteAccess] = $siteAccessPath;

        return $this;
    }

    /**
     * @return array
     */
    public function getHostUriMatchList()
    {
        return $this->hostUriMatchList;
    }

    /**
     * @param $host
     * @param $uri
     * @param $siteaccess
     *
     * @return $this
     */
    public function addHostUriMatchList($host, $uri, $siteaccess)
    {
        $this->hostUriMatchList[$siteaccess][$host] = $uri;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMainDomain()
    {
        return $this->mainDomain;
    }

    /**
     * @param mixed $mainDomain
     *
     * @return Instance
     */
    public function setMainDomain($mainDomain)
    {
        $this->mainDomain = $mainDomain;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMainSiteaccess()
    {
        return $this->mainSiteaccess;
    }

    /**
     * @param mixed $mainSiteaccess
     *
     * @return Instance
     */
    public function setMainSiteaccess($mainSiteaccess)
    {
        $this->mainSiteaccess = $mainSiteaccess;

        return $this;
    }

    /**
     * @return array
     */
    public function getTags()
    {
        return $this->tags;
    }

    public function addTags(array $tags)
    {
        $this->tags = array_unique(array_merge(
            $this->tags,
            $tags
        ));

        return $this;
    }

    /**
     * @return mixed
     */
    public function getVarDirList()
    {
        return $this->varDirList;
    }

    public function addVarDir($siteaccess, $varDir)
    {
        $this->varDirList[$siteaccess] = $varDir;

        return $this;
    }


    /**
     * @return mixed
     */
    public function getCacheDirList()
    {
        return $this->cacheDirList;
    }

    public function addCacheDir($siteaccess, $cacheDir)
    {
        $this->cacheDirList[$siteaccess] = $cacheDir;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getStorageDirList()
    {
        return $this->storageDirList;
    }

    public function addStorageDir($siteaccess, $storageDir)
    {
        $this->storageDirList[$siteaccess] = $storageDir;

        return $this;
    }

    public function getDbList()
    {
        return $this->dbList;
    }

    public function addDb($siteaccess, $db)
    {
        $this->dbList[$siteaccess] = $db;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDbHostList()
    {
        return $this->dbHostList;
    }

    public function addDbHost($siteaccess, $dbHost)
    {
        $this->dbHostList[$siteaccess] = $dbHost;

        return $this;
    }

    public function getDbTypeList()
    {
        return $this->dbTypeList;
    }

    public function addDbType($siteaccess, $dbType)
    {
        $this->dbTypeList[$siteaccess] = $dbType;

        return $this;
    }

    public function getDbPortList()
    {
        return $this->dbPortList;
    }

    public function addDbPort($siteaccess, $dbPortList)
    {
        $this->dbPortList[$siteaccess] = $dbPortList;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDbUserList()
    {
        return $this->dbUserList;
    }

    public function addDbUser($siteaccess, $dbUser)
    {
        $this->dbUserList[$siteaccess] = $dbUser;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSolrHostList()
    {
        return $this->solrHostList;
    }

    public function addSolrHost($siteaccess, $solrHost)
    {
        $this->solrHostList[$siteaccess] = $solrHost;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getNameList()
    {
        return $this->nameList;
    }

    public function addName($siteaccess, $name)
    {
        $this->nameList[$siteaccess] = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDesignListList()
    {
        return $this->designListList;
    }

    public function addDesignList($siteaccess, $list)
    {
        $this->designListList[$siteaccess] = $list;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getVarDir()
    {
        return $this->varDir;
    }

    /**
     * @param mixed $varDir
     */
    public function setVarDir($varDir)
    {
        $this->varDir = $varDir;
    }

    /**
     * @return mixed
     */
    public function getCacheDir()
    {
        return $this->cacheDir;
    }

    /**
     * @param mixed $cacheDir
     */
    public function setCacheDir($cacheDir)
    {
        $this->cacheDir = $cacheDir;
    }

    /**
     * @return mixed
     */
    public function getStorageDir()
    {
        return $this->storageDir;
    }

    /**
     * @param mixed $storageDir
     */
    public function setStorageDir($storageDir)
    {
        $this->storageDir = $storageDir;
    }

    /**
     * @return mixed
     */
    public function getBackendSiteAccess()
    {
        return $this->backendSiteAccess;
    }

    /**
     * @param mixed $backendSiteAccess
     */
    public function setBackendSiteAccess($backendSiteAccess)
    {
        $this->backendSiteAccess = $backendSiteAccess;
    }

    /**
     * @return mixed
     */
    public function getDb()
    {
        return $this->db;
    }

    /**
     * @param mixed $db
     */
    public function setDb($db)
    {
        $this->db = $db;
    }

    /**
     * @return mixed
     */
    public function getDbType()
    {
        return $this->dbType;
    }

    /**
     * @param mixed $dbType
     */
    public function setDbType($dbType)
    {
        $this->dbType = $dbType;
    }

    /**
     * @return mixed
     */
    public function getDbHost()
    {
        return $this->dbHost;
    }

    /**
     * @param mixed $dbHost
     */
    public function setDbHost($dbHost)
    {
        $this->dbHost = $dbHost;
    }

    /**
     * @return mixed
     */
    public function getDbPort()
    {
        return $this->dbPort;
    }

    /**
     * @param mixed $dbPort
     */
    public function setDbPort($dbPort)
    {
        $this->dbPort = $dbPort;
    }

    /**
     * @return mixed
     */
    public function getDbUser()
    {
        return $this->dbUser;
    }

    /**
     * @param mixed $dbUser
     */
    public function setDbUser($dbUser)
    {
        $this->dbUser = $dbUser;
    }

    /**
     * @return mixed
     */
    public function getSolrHost()
    {
        return $this->solrHost;
    }

    /**
     * @param mixed $solrHost
     */
    public function setSolrHost($solrHost)
    {
        $this->solrHost = $solrHost;
    }

    public static function __set_state($array)
    {
        $instance = new Instance();
        foreach($array as $key => $value){
            if (property_exists($instance, $key)){
                $instance->{$key} = $value;
            }
        }

        return $instance;
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }

    /**
     * @return mixed
     */
    public function getDomains()
    {
        return $this->domains;
    }

    /**
     * @param mixed $domains
     *
     * @return Instance
     */
    public function setDomains($domains)
    {
        $this->domains = $domains;

        return $this;
    }

    public function addDomain($domain)
    {
        $this->domains[] = $domain;
        $this->domains = array_unique($this->domains);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getVirtualHost()
    {
        return $this->virtualHost;
    }

    /**
     * @param mixed $virtualHost
     *
     * @return Instance
     */
    public function setVirtualHost($virtualHost)
    {
        $this->virtualHost = $virtualHost;

        return $this;
    }

}
