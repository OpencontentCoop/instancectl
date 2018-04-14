<?php

namespace Opencontent\InstanceCtl\Tools;


class Config
{
    public $group_name;

    public $document_root;

    public $server_name;

    public $web_server_type = 'nginx';

    public $virtual_host_path = '/etx/nginx/sites-enabled';

    public $staging_url_suffix = '.opencontent.it';

    public $instances_filename = 'instances.yml';

    public $cache_filename = 'instancectl.cache';

    public $instancectl_gateway;

    public $instancectl_server_type = 'ez';

    public $instancectl_server_uri;

    public $instancectl_server_username;

    public $instancectl_server_password;

    public function __construct(array $input = null)
    {
        $this->checkInput($input);
    }

    private function checkInput($input)
    {
        $defaults = get_object_vars($this);
        if (is_array($input)) {
            foreach ($defaults as $key => $value) {
                if ($input[$key]) {
                    $this->{$key} = $input[$key];
                }
            }
        }
    }

    public function toArray()
    {
        return get_object_vars($this);
    }
}
