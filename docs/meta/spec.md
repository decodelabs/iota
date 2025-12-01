# Iota — Package Specification

> **Cluster:** `io`
> **Language:** `php`
> **Milestone:** `m3`
> **Repo:** `https://github.com/decodelabs/iota`
> **Role:** Code cache repository

## Overview

### Purpose

Iota provides a discrete, dependable repository system for storing, retrieving, and loading generated code. It enables applications to:

- Store generated PHP code in organized repositories
- Retrieve code as strings or include/return it directly
- Manage static (read-only in production) and dynamic (always writable) repositories
- Store static arrays using Hatch's code generation
- Organize generated code by repository name and key
- Integrate with Genesis build system for deployment

Iota is designed to be a reliable storage mechanism for code generation tools, caching systems, and any application that needs to persist generated PHP code in a structured, file-based repository.

### Non-Goals

- Iota does not provide code generation capabilities (uses Hatch for array generation)
- It does not provide code compilation or optimization
- It does not handle code versioning or history
- It does not provide distributed or networked storage
- It does not implement code validation or linting
- It does not provide code minification or obfuscation

## Role in the Ecosystem

### Cluster & Positioning

Iota belongs to the **io** cluster, providing file-based storage for generated code. It sits alongside other IO packages like Atlas (filesystem operations), Stash (caching), and Iota (code repository).

### Usage Contexts

Iota is used for:

- Code generation tools that need to persist generated code
- Caching systems that store generated PHP code
- Build systems that generate and deploy code
- Applications that generate configuration or data files as PHP code
- Tools that cache expensive computations as executable code
- Framework components that generate optimized code paths

## Public Surface

### Key Types

- **`Iota`** — Main service class implementing `PureService`. Manages static and dynamic repository directories and provides factory methods for creating repositories.

- **`Iota\Repository`** — Class representing a code repository. Provides methods for storing, retrieving, including, and managing generated code files.

- **`Genesis\Build\Provider\Iota`** — Genesis build provider that scans `.iota` directories for deployment.

### Main Entry Points

- **`Iota::__construct(?Dir $staticDir, ?Dir $dynamicDir)`** — Creates an Iota service instance. Defaults static directory to `{run}/.iota` and dynamic directory to `{localData}/iota`.

- **`Iota::load(string $name, string|Dir $dir)`** — Creates a repository with a custom directory location.

- **`Iota::loadStatic(string $name, ?bool $mutable)`** — Creates a static repository. Mutable only in development mode by default.

- **`Iota::loadDynamic(string $name)`** — Creates a dynamic repository that is always writable.

- **`Repository::store(string $key, string $code)`** — Stores generated code under the given key.

- **`Repository::storeStaticArray(string $key, array $data)`** — Stores a static array using Hatch's code generation.

- **`Repository::fetch(string $key)`** — Retrieves code as a string, or null if not found.

- **`Repository::include(string $key)`** — Includes the code file using `require`.

- **`Repository::return(string $key)`** — Includes the code file and returns its value using `require`.

- **`Repository::returnAsType(string $key, string $type)`** — Returns code and coerces it to the specified type.

- **`Repository::has(string $key)`** — Checks if a key exists in the repository.

- **`Repository::remove(string $key)`** — Removes a key from the repository.

- **`Repository::purge()`** — Removes all keys from the repository.

- **`Repository::scan(?callable $filter)`** — Scans repository keys, optionally filtered.

## Dependencies

### Decode Labs

- **`atlas`** — Used for file and directory operations to manage repository storage.

- **`coercion`** — Used for type coercion in `returnAsType()` method.

- **`exceptional`** — Used for exception handling throughout the package.

- **`hatch`** — Used for generating static array code in `storeStaticArray()` method.

- **`kingdom`** — Used for service container integration via `PureService` interface.

- **`monarch`** — Used to determine application paths and development mode for repository mutability.

### External

- No external dependencies beyond Decode Labs packages.

## Behaviour & Contracts

### Invariants

- Repository directories are created automatically if they don't exist
- Keys must not contain reserved characters: `{}()/\@:`
- Static repositories are read-only in production mode unless explicitly set to mutable
- Dynamic repositories are always writable
- Code files are stored as plain PHP files
- `include()` and `return()` operations use `require` (not `include`) for strict error handling
- Repository mutability is checked before write operations

### Input & Output Contracts

- **`load(string $name, string|Dir $dir): Repository`** — Creates a repository with the given name and directory. Directory is created if it doesn't exist. Returns a mutable repository.

- **`loadStatic(string $name, ?bool $mutable): Repository`** — Creates a static repository. Mutable defaults to development mode. Returns a repository instance.

- **`loadDynamic(string $name): Repository`** — Creates a dynamic repository that is always writable. Returns a repository instance.

- **`store(string $key, string $code): void`** — Stores code string under the given key. Throws `Runtime` exception if repository is read-only. Throws `InvalidArgument` if key contains reserved characters.

- **`storeStaticArray(string $key, array $data): void`** — Stores a static array using Hatch's code generation. Wraps the generated code in a PHP file that returns the array.

- **`fetch(string $key): ?string`** — Returns code as a string, or null if key doesn't exist. Throws `InvalidArgument` if key contains reserved characters.

- **`include(string $key): void`** — Includes the code file using `require`. Throws `NotFound` if key doesn't exist. Throws `InvalidArgument` if key contains reserved characters.

- **`return(string $key): mixed`** — Includes the code file and returns its value. Throws `NotFound` if key doesn't exist. Throws `InvalidArgument` if key contains reserved characters.

- **`returnAsType(string $key, string $type): object`** — Returns code and coerces it to the specified type. Throws `UnexpectedValue` if coercion fails. Throws `NotFound` if key doesn't exist.

- **`has(string $key): bool`** — Returns true if key exists, false otherwise. Returns false if key contains reserved characters.

- **`remove(string $key): void`** — Removes a key from the repository. Throws `Runtime` exception if repository is read-only. Throws `InvalidArgument` if key contains reserved characters.

- **`purge(): void`** — Removes all keys from the repository. Throws `Runtime` exception if repository is read-only.

- **`scan(?callable $filter): Generator<string>`** — Returns a generator of key names, optionally filtered. Throws `InvalidArgument` if filter callback is invalid.

## Error Handling

Iota uses the Exceptional pattern for error handling. Key exception types:

- **`Runtime`** — Thrown when write operations are attempted on read-only repositories.

- **`NotFound`** — Thrown when `include()` or `return()` is called with a non-existent key.

- **`InvalidArgument`** — Thrown when keys contain reserved characters or when filter callbacks are invalid.

- **`UnexpectedValue`** — Thrown when `returnAsType()` fails to coerce the returned value to the specified type.

Exceptions preserve the original service context and include detailed error messages.

## Configuration & Extensibility

### Extension Points

- **Custom Repository Directories** — Use `load()` with custom directory paths to create repositories in any location.

- **Repository Mutability** — Control repository mutability via `loadStatic()` with explicit `mutable` parameter.

- **Key Filtering** — Use `scan()` with custom filter callbacks to discover specific keys.

- **Code Generation** — Integrate with Hatch or other code generation tools to create code before storing.

### Configuration

- **Static Directory** — Defaults to `{run}/.iota` but can be overridden in constructor. Used for application-level repositories that are read-only in production.

- **Dynamic Directory** — Defaults to `{localData}/iota` but can be overridden in constructor. Used for application-level repositories that are always writable.

- **Development Mode** — Static repositories are mutable in development mode by default, read-only in production.

- **Key Validation** — Keys are validated to prevent directory traversal and reserved character usage.

## Interactions with Other Packages

- **Atlas** — Used for file and directory operations to manage repository storage.

- **Hatch** — Used for generating static array code in `storeStaticArray()` method.

- **Monarch** — Used to determine application paths and development mode.

- **Kingdom** — Integrated as a pure service, allowing automatic resolution from the service container.

- **Genesis** — Integrated via `Genesis\Build\Provider\Iota` to include `.iota` directories in build deployments.

- **Coercion** — Used for type coercion in `returnAsType()` method.

## Usage Examples

### Basic Repository Usage

```php
use DecodeLabs\Iota;

$iota = new Iota();

// Load a static repository (read-only in production)
$repo = $iota->loadStatic('cache');

// Store generated code
$repo->store(
    'config',
    <<<'PHP'
    <?php
    return [
        'app' => 'MyApp',
        'version' => '1.0.0'
    ];
    PHP
);

// Retrieve code as string
$code = $repo->fetch('config');

// Include and return value
$config = $repo->return('config');
```

### Dynamic Repository

```php
use DecodeLabs\Iota;

$iota = new Iota();

// Load a dynamic repository (always writable)
$repo = $iota->loadDynamic('generated');

// Store code
$repo->store('helper', '<?php function helper() { return "Hello"; }');

// Include code
$repo->include('helper');
helper(); // "Hello"
```

### Static Array Storage

```php
use DecodeLabs\Iota;

$iota = new Iota();
$repo = $iota->loadStatic('data');

// Store static array using Hatch
$repo->storeStaticArray('routes', [
    'home' => '/',
    'about' => '/about',
    'contact' => '/contact'
]);

// Retrieve as array
$routes = $repo->return('routes');
```

### Custom Repository Location

```php
use DecodeLabs\Iota;
use DecodeLabs\Atlas;

$iota = new Iota();

// Load repository from custom location
$repo = $iota->load(
    'custom',
    Atlas::getDir('/path/to/custom/repo')
);

$repo->store('data', '<?php return "custom data";');
```

### Repository Management

```php
use DecodeLabs\Iota;

$iota = new Iota();
$repo = $iota->loadDynamic('temp');

// Check if key exists
if ($repo->has('key')) {
    // Get modification time
    $time = $repo->getTime('key');
}

// Scan all keys
foreach ($repo->scan() as $key) {
    echo $key;
}

// Scan with filter
foreach ($repo->scan(fn($key) => str_starts_with($key, 'prefix_')) as $key) {
    echo $key;
}

// Remove key
$repo->remove('key');

// Purge all keys
$repo->purge();
```

### Type-Safe Returns

```php
use DecodeLabs\Iota;

$iota = new Iota();
$repo = $iota->loadStatic('entities');

// Return and coerce to type
$entity = $repo->returnAsType('user', User::class);
```

### Genesis Integration

```php
// Genesis automatically scans .iota directories via
// Genesis\Build\Provider\Iota when building for deployment
```

## Implementation Notes (for Contributors)

### Architecture

- **Repository Structure** — Each repository is a directory containing PHP files. Keys map directly to filenames.

- **Static vs Dynamic** — Static repositories are intended for code generated during build/deployment and should be read-only in production. Dynamic repositories are for runtime-generated code.

- **Key Validation** — Keys are validated to prevent directory traversal (`/`, `\`) and reserved characters that could cause issues in file paths or PHP includes.

- **Code Generation** — `storeStaticArray()` uses Hatch to generate optimized PHP array code, which is more efficient than serialization for large arrays.

- **Include Mechanism** — Uses `require` (not `include`) to ensure errors are thrown if files don't exist, providing strict error handling.

- **Directory Creation** — Repository directories are created automatically on construction to ensure they exist.

- **Genesis Integration** — The Genesis build provider scans `.iota` directories to include generated code in deployments.

### Performance Considerations

- Direct file I/O for storage and retrieval provides fast access
- Static array generation via Hatch creates optimized PHP code
- No serialization overhead for array storage
- File-based storage allows easy inspection and debugging

### Design Decisions

- **File-Based Storage** — Using files provides transparency, easy debugging, and integration with version control and deployment systems.

- **Static vs Dynamic Separation** — Separating static (build-time) and dynamic (runtime) repositories provides clear boundaries and deployment safety.

- **Key Validation** — Strict key validation prevents security issues and ensures reliable file operations.

- **Pure Service** — Implementing `PureService` allows Iota to be used as a stateless service in the Kingdom container.

- **Hatch Integration** — Using Hatch for array generation provides consistent code generation across the ecosystem.

## Testing & Quality

**Code Quality:** 4/5 — Mature, well-structured codebase with comprehensive functionality and type safety.

**README Quality:** 3/5 — Good documentation with clear usage examples covering main use cases.

**Documentation:** 0/5 — No formal documentation beyond README.

**Tests:** 0/5 — No test suite currently.

See `composer.json` for supported PHP versions.

## Roadmap & Future Ideas

- Enhanced documentation and API reference
- Test suite implementation
- Code validation and linting integration
- Support for code minification
- Repository versioning and history
- Performance optimizations for large repositories
- Support for code compression
- Repository metadata and indexing

## References

- [Decode Labs Chorus](https://github.com/decodelabs/chorus)
- [Iota Repository](https://github.com/decodelabs/iota)

