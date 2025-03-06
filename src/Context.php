<?php

/**
 * @package Iota
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Iota;

use DecodeLabs\Atlas;
use DecodeLabs\Atlas\Dir;
use DecodeLabs\Genesis;
use DecodeLabs\Iota;
use DecodeLabs\Veneer;

class Context {

    protected(set) Dir $staticDir;
    protected(set) Dir $dynamicDir;

    public function __construct(
        ?Dir $staticDir = null,
        ?Dir $dynamicDir = null
    ) {
        if($staticDir === null) {
            if(class_exists(Genesis::class)) {
                $staticDir = Genesis::$hub->applicationPath;
            } else {
                $staticDir = getcwd();
            }

            $staticDir = Atlas::dir($staticDir.'/.iota');
        }

        if($dynamicDir === null) {
            if(class_exists(Genesis::class)) {
                $dynamicDir = Genesis::$hub->localDataPath.'/iota';
            } else {
                $dynamicDir = sys_get_temp_dir().'/decodelabs/iota';
            }

            $dynamicDir = Atlas::dir($dynamicDir);
        }

        $this->staticDir = $staticDir;
        $this->dynamicDir = $dynamicDir;
    }

    public function load(
        string $name,
        string|Dir $dir
    ): Repository {
        if(is_string($dir)) {
            $dir = Atlas::dir($dir);
        }

        return new Repository($name, $dir);
    }

    public function loadStatic(
        string $name,
        ?bool $mutable = null
    ): Repository {
        if($mutable === null) {
            if(class_exists(Genesis::class)) {
                $mutable = Genesis::$environment->isDevelopment();
            } else {
                $mutable = false;
            }
        }

        return new Repository(
            name: $name,
            dir: $this->staticDir->getDir($name),
            mutable: $mutable
        );
    }

    public function loadDynamic(
        string $name
    ): Repository {
        return new Repository(
            name: $name,
            dir: $this->dynamicDir->getDir($name)
        );
    }
}

// Register the Veneer facade
Veneer\Manager::getGlobalManager()->register(
    Context::class,
    Iota::class
);
