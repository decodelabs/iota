<?php

/**
 * @package Iota
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Iota;

use DecodeLabs\Atlas\Dir;
use DecodeLabs\Coercion;
use DecodeLabs\Exceptional;
use DecodeLabs\Hatch;
use DecodeLabs\Hatch\Representation\StaticExpression as StaticExpressionRepresentation;
use DecodeLabs\Hatch\Proxy\StaticExpression as StaticExpressionProxy;
use Generator;
use Throwable;

class Repository {

    protected(set) string $name;
    protected(set) Dir $dir;
    protected(set) bool $mutable;

    public function __construct(
        string $name,
        Dir $dir,
        bool $mutable = true
    ) {
        $this->name = $name;
        $this->dir = $dir;
        $this->mutable = $mutable;

        $this->dir->ensureExists();
    }

    public function has(
        string $key
    ): bool {
        try {
            $this->checkKey($key);
        } catch(Throwable $e) {
            return false;
        }

        return $this->dir->getFile($key)->exists();
    }

    public function store(
        string $key,
        string $code
    ): void {
        if(!$this->mutable) {
            throw Exceptional::Runtime(
                'Iota repository \'' . $this->name . '\' is read only'
            );
        }

        $this->checkKey($key);

        $file = $this->dir->getFile($key);
        $file->putContents($code);
    }

    /**
     * @param array<int|string,bool|float|int|array<mixed>|string|StaticExpressionRepresentation|StaticExpressionProxy|null> $data
     */
    public function storeStaticArray(
        string $key,
        array $data
    ): void {
        $export = Hatch::exportStaticArray($data);

        $this->store(
            $key,
            <<<PHP
            <?php
            return {$export};
            PHP
        );
    }

    public function fetch(
        string $key
    ): ?string {
        $this->checkKey($key);

        $file = $this->dir->getFile($key);

        if(!$file->exists()) {
            return null;
        }

        return $file->getContents();
    }

    public function remove(
        string $key
    ): void {
        if(!$this->mutable) {
            throw Exceptional::Runtime(
                'Iota repository \'' . $this->name . '\' is read only'
            );
        }

        $this->checkKey($key);

        $file = $this->dir->getFile($key);

        if($file->exists()) {
            $file->delete();
        }
    }

    public function purge(): void
    {
        if(!$this->mutable) {
            throw Exceptional::Runtime(
                'Iota repository \'' . $this->name . '\' is read only'
            );
        }

        $this->dir->emptyOut();
    }

    /**
     * @return Generator<string>
     */
    public function scan(
        ?callable $filter = null
    ): Generator {
        return $this->dir->scanNames($filter);
    }

    public function include(
        string $key
    ): void {
        $this->checkKey($key);

        $file = $this->dir->getFile($key);

        if(!$file->exists()) {
            throw Exceptional::NotFound(
                'Iota repository \'' . $this->name . '\' does not contain \'' . $key . '\''
            );
        }

        require $file->getPath();
    }

    public function return(
        string $key
    ): mixed {
        $this->checkKey($key);

        $file = $this->dir->getFile($key);

        if(!$file->exists()) {
            throw Exceptional::NotFound(
                'Iota repository \'' . $this->name . '\' does not contain \'' . $key . '\''
            );
        }

        return require $file->getPath();
    }

    /**
     * @template T of object
     * @param string $key
     * @param class-string<T> $type
     * @return T
     */
    public function returnAsType(
        string $key,
        string $type
    ): object {
        $output = $this->return($key);

        try {
            return Coercion::asType($output, $type);
        } catch(Throwable $e) {
            throw Exceptional::UnexpectedValue(
                message: 'Iota repository \'' . $this->name . '\' returned unexpected value for \'' . $key . '\': ' . $e->getMessage(),
                data: $output
            );
        }
    }

    private function checkKey(
        string $key
    ): void {
        if (preg_match('|[\{\}\(\)/\\\@\:]|', $key)) {
            throw Exceptional::InvalidArgument(
                message: 'Key must not contain reserved extension characters: {}()/\@:',
                data: $key
            );
        }
    }
}
