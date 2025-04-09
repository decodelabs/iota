<?php

/**
 * @package Iota
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Iota;

use DecodeLabs\Atlas;
use DecodeLabs\Atlas\Dir;
use DecodeLabs\Iota;
use DecodeLabs\Monarch;
use DecodeLabs\Veneer;

class Context {

    protected(set) Dir $staticDir;
    protected(set) Dir $dynamicDir;

    public function __construct(
        ?Dir $staticDir = null,
        ?Dir $dynamicDir = null
    ) {
        if($staticDir === null) {
            $staticDir = Atlas::dir(
                Monarch::$paths->run.'/.iota'
            );
        }

        if($dynamicDir === null) {
            $dynamicDir = Atlas::dir(
                Monarch::$paths->localData.'/iota'
            );
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
        return new Repository(
            name: $name,
            dir: $this->staticDir->getDir($name),
            mutable: $mutable ?? Monarch::isDevelopment()
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
