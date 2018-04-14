<?php

namespace Opencontent\InstanceCtl\Tools;

use Opencontent\InstanceCtl\Tools\Instance;

class InstanceExporter
{
    public function toYamlArray(Instance $instance)
    {
        return array(
            "url" => $instance->getMainDomain(),
            "url_staging" => $instance->getIdentifier() . ConfigHandler::current()->staging_url_suffix,
            "var_dir" => $instance->getVarDir(),
            "cache_dir" => $instance->getCacheDir(),
            "storage_dir" => $instance->getStorageDir(),
            "main_siteaccess" => $instance->getMainSiteaccess(),
            "script_siteaccess" => $instance->getBackendSiteaccess(),
            "site_access" => array_keys($instance->getSiteAccessList()),
            "db_host" => $instance->getDbHost(),
            "db_port" => $instance->getDbPort(),
            "db_type" => $instance->getDbType(),
            "db_name" => $instance->getDb(),
            "db_user" => $instance->getDbUser(),
            "solr_host" => $instance->getSolrHost(),
            "tags" => $instance->getTags(),
        );
    }

    public function toRemotePayloadArray(Instance $instance)
    {
        return $instance->jsonSerialize();
    }
}
