# Iota

[![PHP from Packagist](https://img.shields.io/packagist/php-v/decodelabs/iota?style=flat)](https://packagist.org/packages/decodelabs/iota)
[![Latest Version](https://img.shields.io/packagist/v/decodelabs/iota.svg?style=flat)](https://packagist.org/packages/decodelabs/iota)
[![Total Downloads](https://img.shields.io/packagist/dt/decodelabs/iota.svg?style=flat)](https://packagist.org/packages/decodelabs/iota)
[![GitHub Workflow Status](https://img.shields.io/github/actions/workflow/status/decodelabs/iota/integrate.yml?branch=develop)](https://github.com/decodelabs/iota/actions/workflows/integrate.yml)
[![PHPStan](https://img.shields.io/badge/PHPStan-enabled-44CC11.svg?longCache=true&style=flat)](https://github.com/phpstan/phpstan)
[![License](https://img.shields.io/packagist/l/decodelabs/iota?style=flat)](https://packagist.org/packages/decodelabs/iota)

### Discrete, dependable generated code repository

Iota provides a simple mechanism for storing, retrieving and loading generated code.

---

## Installation

Install via Composer:

```bash
composer require decodelabs/iota
```

## Usage

Load a repository:

```php
use DecodeLabs\Iota;

// Load manually
$repo = Iota::load('name', '/path/to/repo');

// Load an app-level repository only writable in dev mode
$repo = Iota::loadStatic('name');

// Load a writable app-level repository
$repo = Iota::loadDynamic('name');
```

Then you can store and retrieve code:

```php
// Store anything
$repo->store(
    'key',
    <<<'PHP'
    <?php
    return 'Hello, world!';
    PHP
);

// Retrieve code
$code = $repo->fetch('key');

// Include quietly
$repo->include('key');

// Include and return
$value = $repo->Return('key'); // 'Hello, world!'
```

## Licensing

Iota is licensed under the MIT License. See [LICENSE](./LICENSE) for the full license text.
