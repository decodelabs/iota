<?php

/**
 * Iota
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs;

use DecodeLabs\Atlas\Dir;
use DecodeLabs\Iota\Repository;
use DecodeLabs\Kingdom\PureService;
use DecodeLabs\Kingdom\PureServiceTrait;

class Iota implements PureService
{
    use PureServiceTrait;

    public protected(set) Dir $staticDir;
    public protected(set) Dir $dynamicDir;

    public function __construct(
        ?Dir $staticDir = null,
        ?Dir $dynamicDir = null
    ) {
        if ($staticDir === null) {
            $staticDir = Atlas::getDir(
                Monarch::getPaths()->run . '/.iota'
            );
        }

        if ($dynamicDir === null) {
            $dynamicDir = Atlas::getDir(
                Monarch::getPaths()->localData . '/iota'
            );
        }

        $this->staticDir = $staticDir;
        $this->dynamicDir = $dynamicDir;
    }

    public function load(
        string $name,
        string|Dir $dir
    ): Repository {
        if (is_string($dir)) {
            $dir = Atlas::getDir($dir);
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
