<?php

/**
 * @package Iota
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Genesis\Build\Provider;

use DecodeLabs\Atlas\Dir;
use DecodeLabs\Genesis\Build\Provider;
use Generator;

class Iota implements Provider
{
    public string $name = 'iota';

    public function __construct()
    {
    }

    public function scanBuildItems(
        Dir $rootDir
    ): Generator {
        yield $rootDir->getDir('.iota') => '.iota/';
    }
}
