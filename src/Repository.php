<?php

/**
 * @package Iota
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Iota;

use DecodeLabs\Atlas\Dir;
use DecodeLabs\Exceptional;

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
