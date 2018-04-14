<?php

namespace Opencontent\InstanceCtl\Tools;

use Symfony\Component\Finder\Finder;
use SplFileInfo;
use RomanPitak\Nginx\Config\Scope;
use RomanPitak\Nginx\Config\Directive;

class NginxParser
{
    private static $vhosts;

    private $findPaths = array();

    private $results = array();

    public function find($url)
    {
        $this->load();

        $this->results = array();
        $this->findPaths = array();
        foreach (self::$vhosts as $vhost) {
            if (in_array(trim($url), $vhost['aliases'])){
                $this->results[] = $vhost;
                $this->findPaths[] = $vhost['path'];
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getFindPaths()
    {
        return $this->findPaths;
    }

    /**
     * @return array
     */
    public function getResults()
    {
        return $this->results;
    }


    private function load()
    {
        if (self::$vhosts === null) {
            $sourceFinder = new Finder();
            $sourceFinder->files()->in(ConfigHandler::current()->virtual_host_path);

            /** @var SplFileInfo $file */
            foreach ($sourceFinder as $file) {
                $path = $file->getPath() . $file->getFilename();
                $scope = Scope::fromFile($path);
                self::$vhosts[] = array(
                    'path' => $path,
                    'aliases' => $this->findAliases($scope)
                );
            }
        }

        return self::$vhosts;
    }

    private function findAliases(Scope $scope)
    {
        $aliases = array();
        foreach ($scope->getDirectives() as $directive) {
            $aliases = array_merge(
                $aliases,
                $this->findAliasesInDirective($directive)
            );
        }

        return $aliases;
    }

    private function findAliasesInDirective(Directive $directive)
    {
        $aliases = array();
        if ($directive->getChildScope() instanceof Scope) {
            foreach ($directive->getChildScope()->getDirectives() as $subDirective) {
                $aliases = array_merge(
                    $aliases,
                    $this->findAliasesInDirective($subDirective)
                );
            }
        } else {
            if (strpos((string)$directive, 'server_name') !== false) {
                $cleanDirective = str_replace('server_name', '', $directive);
                $aliasList = explode(' ', trim($cleanDirective, " \t\n;"));
                $aliases = array_merge(
                    $aliases,
                    $aliasList
                );
            }
        }

        return array_unique($aliases);
    }
}
