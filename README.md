# twig-front-matter

[![Integrate](https://github.com/ergebnis/twig-front-matter/workflows/Integrate/badge.svg)](https://github.com/ergebnis/twig-front-matter/actions)
[![Merge](https://github.com/ergebnis/twig-front-matter/workflows/Merge/badge.svg)](https://github.com/ergebnis/twig-front-matter/actions)
[![Release](https://github.com/ergebnis/twig-front-matter/workflows/Release/badge.svg)](https://github.com/ergebnis/twig-front-matter/actions)
[![Renew](https://github.com/ergebnis/twig-front-matter/workflows/Renew/badge.svg)](https://github.com/ergebnis/twig-front-matter/actions)

[![Code Coverage](https://codecov.io/gh/ergebnis/twig-front-matter/branch/main/graph/badge.svg)](https://codecov.io/gh/ergebnis/twig-front-matter)
[![Type Coverage](https://shepherd.dev/github/ergebnis/twig-front-matter/coverage.svg)](https://shepherd.dev/github/ergebnis/twig-front-matter)

[![Latest Stable Version](https://poser.pugx.org/ergebnis/twig-front-matter/v/stable)](https://packagist.org/packages/ergebnis/twig-front-matter)
[![Total Downloads](https://poser.pugx.org/ergebnis/twig-front-matter/downloads)](https://packagist.org/packages/ergebnis/twig-front-matter)
[![Monthly Downloads](http://poser.pugx.org/ergebnis/twig-front-matter/d/monthly)](https://packagist.org/packages/ergebnis/twig-front-matter)

This package provides a [Twig](https://twig.symfony.com) loader for files with [YAML front-matter](https://github.com/ergebnis/front-matter).

## Installation

Run

```sh
composer require ergebnis/twig-front-matter
```

## Usage

### Loading Twig templates with the `FrontMatterLoader`

This package ships with a [`FrontMatterLoader`](/src/FrontMatterLoader.php) that you can use to load Twig templates with YAML front-matter.

The `FrontMatterLoader`

- parses a Twig template and separates the front-matter from the body-matter using [`ergebnis/front-matter](https://github.com/ergebnis/front-matter)
- converts the front-matter data to Twig assignments using an implementation of  [`Converter\FrontMatterConverter`](/src/Converter/FrontMatterConverter.php)
- returns a new Twig Source that merges the Twig assignments from the front-matter data with the body-matter from the Twig template

### YAML front-matter

Assume that you have a Twig template with the following YAML front-matter:

```yaml
foo: bar
number: 1234
pi: 3.14159
date: 2016-05-27
empty: ~
invalid-key: "hmm"
multiline: |
  Multiple
  Line
  String
object:
  key: value
  datetime: 2020-11-12 12:54:12
  values:
    - one
    - two
```

### Converting YAML front-matter to Twig assignments with the `Converter/ToMultipleAssignmentsFrontMatterConverter`

This package ships with a [`Converter/ToMultipleAssignmentsFrontMatterConverter.php`](/src/Converter/ToMultipleAssignmentsFrontMatterConverter.php) that you can use to convert the YAML front-matter to multiple Twig assignments.

The example below will convert the parsed YAML front-matter data to multiple Twig assignments that will assign data to Twig variables with force:

```php

declare(strict_types=1);

use Ergebnis\Twig;

$frontMatterConverter = new Twig\FrontMatter\Converter\ToMultipleAssignmentsFrontMatterConverter(true);

echo $frontMatterConverter->convert($data);
```

```twig
{% set foo = "bar" %}
{% set number = 1234 %}
{% set pi = 3.14159 %}
{% set date = (1464307200|date_modify('0sec')) %}
{% set empty = null %}
{% set multiline = "Multiple\nLine\nString\n" %}
{% set object = { key: "value", datetime: (1605185652|date_modify('0sec')), values: { 0: "one", 1: "two" } } %}
```

The example below will convert the parsed YAML front-matter data to multiple Twig assignments that will assign data to Twig variables without force (taking into account that you may pass variables to the template and prefer not to override these variables with front-matter):

```php

declare(strict_types=1);

use Ergebnis\Twig;

$frontMatterConverter = new Twig\FrontMatter\Converter\ToMultipleAssignmentsFrontMatterConverter(false);

echo $frontMatterConverter->convert($data);
```

```twig
{% set foo = foo is defined ? foo : "bar" %}
{% set number = number is defined ? number : 1234 %}
{% set pi = pi is defined ? pi : 3.14159 %}
{% set date = date is defined ? date : (1464307200|date_modify('0sec')) %}
{% set empty = empty is defined ? empty : null %}
{% set multiline = multiline is defined ? multiline : "Multiple\nLine\nString\n" %}
{% set object = object is defined ? object|merge({ key: "value", datetime: (1605185652|date_modify('0sec')), values: { 0: "one", 1: "two" } }) : { key: "value", datetime: (1605185652|date_modify('0sec')), values: { 0: "one", 1: "two" } } %}
```

### Converting YAML front-matter to a Twig assignment with the `Converter/ToSingleAssignmentFrontMatterConverter`

This package ships with a [`Converter/ToSingleAssignmentFrontMatterConverter.php`](/src/Converter/ToSingleAssignmentFrontMatterConverter.php) that you can use to convert the YAML front-matter to a single Twig assignment.

The example below will convert the parsed YAML front-matter data to a single Twig assignment that will assign data to a Twig variable with force:

```php

declare(strict_types=1);

use Ergebnis\Twig;

$frontMatterConverter = new Twig\FrontMatter\Converter\ToSingleAssignmentFrontMatterConverter(
    Twig\Expression\Name::fromString('data'),
    false,
);

echo $frontMatterConverter->convert($data);
```

```twig
{% set data = { foo: "bar", number: 1234, pi: 3.14159, date: (1464307200|date_modify('0sec')), empty: null, invalid-key: "hmm", multiline: "Multiple\\nLine\\nString\\n", object: { key: "value", datetime: (1605185652|date_modify('0sec')), values: { 0: "one", 1: "two" } } } %}
```

The example below will convert the parsed YAML front-matter data to a single Twig assignment that will assign data to a Twig variables without force (taking into account that you may pass a variable to the template and prefer not to override this variable with front-matter):

```php

declare(strict_types=1);

use Ergebnis\Twig;

$frontMatterConverter = new Twig\FrontMatter\Converter\ToSingleAssignmentFrontMatterConverter(
    Twig\Expression\Name::fromString('data'),
    false,
);

echo $frontMatterConverter->convert($data);
```

```twig
{% set data = data is defined ? data|merge({ foo: "bar", number: 1234, pi: 3.14159, date: (1464307200|date_modify('0sec')), empty: null, invalid-key: "hmm", multiline: "Multiple\\nLine\\nString\\n", object: { key: "value", datetime: (1605185652|date_modify('0sec')), values: { 0: "one", 1: "two" } } }) : { foo: "bar", number: 1234, pi: 3.14159, date: (1464307200|date_modify('0sec')), empty: null, invalid-key: "hmm", multiline: "Multiple\\nLine\\nString\\n", object: { key: "value", datetime: (1605185652|date_modify('0sec')), values: { 0: "one", 1: "two" } } } %}
```

### Configuring services in a Symfony project

Adjust your `config/services.php` as follows to register a `FrontMatterLoader` with a `Converter\ToMultipleAssignmentsFrontMatterConverter`:

```php
<?php

declare(strict_types=1);

use Ergebnis\FrontMatter;
use Ergebnis\Twig;
use Symfony\Component\DependencyInjection;

return static function (DependencyInjection\Loader\Configurator\ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->autoconfigure()
        ->autowire();

    $services->alias(
        FrontMatter\Parser::class,
        FrontMatter\YamlParser::class,
    );

    $services->set(FrontMatter\YamlParser::class)
        ->class(FrontMatter\YamlParser::class);


    $services->set(Twig\FrontMatter\Converter\FrontMatterConverter::class)
        ->class(Twig\FrontMatter\Converter\ToMultipleAssignmentsFrontMatterConverter::class)
        ->args([
            '$force' => false, // or true, as you prefer
        ]);

    $services->set(Twig\FrontMatter\FrontMatterLoader::class)
        ->args([
            '$loader' => new DependencyInjection\Loader\Configurator\ReferenceConfigurator('twig.loader.native_filesystem'),
        ]);
});
```

## Changelog

The maintainers of this package record notable changes to this project in a [changelog](CHANGELOG.md).

## Contributing

The maintainers of this package suggest following the [contribution guide](.github/CONTRIBUTING.md).

## Code of Conduct

The maintainers of this package ask contributors to follow the [code of conduct](.github/CODE_OF_CONDUCT.md).

## General Support Policy

The maintainers of this package provide limited support.

You can support the maintenance of this package by [sponsoring @localheinz](https://github.com/sponsors/localheinz) or [requesting an invoice for services related to this package](mailto:am@localheinz.com?subject=ergebnis/twig-front-matter:%20Requesting%20invoice%20for%20services).

## PHP Version Support Policy

This package supports PHP versions with [active support](https://www.php.net/supported-versions.php).

The maintainers of this package add support for a PHP version following its initial release and drop support for a PHP version when it has reached its end of active support.

## Security Policy

This package has a [security policy](.github/SECURITY.md).

## License

This package uses the [MIT license](LICENSE.md).

## Social

Follow [@localheinz](https://twitter.com/intent/follow?screen_name=localheinz) and [@ergebnis](https://twitter.com/intent/follow?screen_name=ergebnis) on Twitter.
