<?php
/**
 * This is a stub file for IDE compatibility only.
 * It should not be included in your projects.
 */
namespace DecodeLabs;

use DecodeLabs\Veneer\Proxy as Proxy;
use DecodeLabs\Veneer\ProxyTrait as ProxyTrait;
use DecodeLabs\Iota\Context as Inst;
use DecodeLabs\Atlas\Dir as Ref0;
use DecodeLabs\Iota\Repository as Ref1;

class Iota implements Proxy
{
    use ProxyTrait;

    public const Veneer = 'DecodeLabs\\Iota';
    public const VeneerTarget = Inst::class;

    protected static Inst $_veneerInstance;

    public static function loadRepository(string $name, Ref0|string $dir): Ref1 {
        return static::$_veneerInstance->loadRepository(...func_get_args());
    }
    public static function loadStaticAppRepository(string $name, ?bool $mutable = NULL): Ref1 {
        return static::$_veneerInstance->loadStaticAppRepository(...func_get_args());
    }
    public static function loadDynamicAppRepository(string $name): Ref1 {
        return static::$_veneerInstance->loadDynamicAppRepository(...func_get_args());
    }
};
