<?php

namespace Opencontent\InstanceCtl\Installers;

class Booking extends AbstractInstaller
{
    public function getName()
    {
        return 'booking';
    }

    public function getActions()
    {
        return array(
            'ini' => array(
                array(
                    'siteaccess' => $this->getInstance()->getIdentifier() . '_backend',
                    'items' => array(
                        array(
                            'file' => 'site.ini',
                            'mode' => 'append',
                            'values' => array(
                                'SiteAccessSettings' => array(
                                    'RelatedSiteAccessList' => array($this->getInstance()->getIdentifier() . "_booking"),
                                ),
                                'ExtensionSettings' => array(
                                    'ActiveAccessExtensions' => array('openpa_booking')
                                )
                            )
                        )

                    )
                ),
                array(
                    'siteaccess' => $this->getInstance()->getIdentifier() . '_booking',
                    'items' => array(
                        array(
                            'file' => 'solr.ini',
                            'values' => array(
                                'SolrBase' => array(
                                    'SearchServerURI' => $this->getInstance()->getSolrHost()
                                )
                            )
                        ),
                        array(
                            'file' => 'site.ini',
                            'values' => array(
                                //...
                            )
                        )
                    )
                )
            )
        );
    }
}
