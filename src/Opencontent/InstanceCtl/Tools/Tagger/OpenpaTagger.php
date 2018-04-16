<?php

namespace Opencontent\InstanceCtl\Tools\Tagger;

use Opencontent\InstanceCtl\Tools\IniLoaderTrait;
use Opencontent\InstanceCtl\Tools\Instance;
use Opencontent\InstanceCtl\Tools\TaggerInterface;

class OpenpaTagger implements TaggerInterface
{
    use IniLoaderTrait;

    public function tag(Instance $instance)
    {
        $siteAccess = $instance->getMainSiteaccess();

        $tags = array();

        $siteIni = $this->getIni('site.ini', $siteAccess);

        $designs = $siteIni->variable('DesignSettings', 'AdditionalSiteDesignList');
        $designs[] = $siteIni->variable('DesignSettings', 'SiteDesign');
        foreach ($designs as $design){
            $tags[] = 'design:'.$design;
        }

        $backendSiteAccess = $instance->getBackendSiteAccess();
        $openpaIni = $this->getIni('openpa.ini', $backendSiteAccess);
        $syncTrasparenza = true;
        if ($openpaIni->hasVariable('NetworkSettings', 'SyncTrasparenza')
            && $openpaIni->variable('NetworkSettings', 'SyncTrasparenza') == 'disabled'){
            $syncTrasparenza = false;
        }
        if ($syncTrasparenza){
            $tags[] = 'features:sync_trasparenza';
        }

        if ($openpaIni->hasVariable('InstanceSettings', 'InstanceType'))
            $tags[] = 'type:' . $openpaIni->variable('InstanceSettings', 'InstanceType');

        foreach ($instance->getSiteAccessList() as $siteAccess => $path) {
            $extensions = $this->getIni('site.ini', $siteAccess)->variable('ExtensionSettings', 'ActiveAccessExtensions');
            foreach ($extensions as $extension) {
                $tags[] = 'extension:' . $extension;
            }
        }

        $instance->addTags(array_unique($tags));
    }
}
