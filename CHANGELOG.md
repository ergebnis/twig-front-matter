# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/), and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## Unreleased

For a full diff see [`1.3.0...main`][1.3.0...main].

## [`1.3.0`][1.3.0]

For a full diff see [`1.2.0...1.3.0`][1.2.0...1.3.0].

### Changed

- Allowed installation on PHP 8.5 ([#343]), by [@localheinz]

## [`1.2.0`][1.2.0]

For a full diff see [`1.1.0...1.2.0`][1.1.0...1.2.0].

### Added

- Added support for PHP 8.4 ([#275]), by [@localheinz]

## [`1.1.0`][1.1.0]

For a full diff see [`1.0.1...1.1.0`][1.0.1...1.1.0].

### Changed

- Required `ergebnis/front-matter:^3.1.0` ([#81]), by [@localheinz]
- Added support for PHP 8.3 ([#82]), by [@localheinz]

## [`1.0.1`][1.0.1]

For a full diff see [`1.0.0...1.0.1`][1.0.0...1.0.1].

### Changed

- Allowed installation of `twig/twig:^3.4.3` ([#8]), by [@localheinz]

## [`1.0.0`][1.0.0]

For a full diff see [`7039e81...1.0.0`][7039e81...1.0.0].

### Added

- Implemented `Expression\Name` as a value object ([#1]), by [@localheinz]
- Implemented `Expression\Value` as a value object ([#2]), by [@localheinz]
- Implemented `Expression\Assignment` as a value object ([#3]), by [@localheinz]
- Implemented `Converter\NullFrontMatterConverter` ([#4]), by [@localheinz]
- Implemented `Converter\ToSingleAssignmentFrontMatterConverter` ([#5]), by [@localheinz]
- Implemented `Converter\ToMultipleAssignmentsFrontMatterConverter` ([#6]), by [@localheinz]
- Implemented `FrontMatterLoader` ([#7]), by [@localheinz]

[1.0.0]: https://github.com/ergebnis/twig-front-matter/releases/tag/1.0.0
[1.0.1]: https://github.com/ergebnis/twig-front-matter/releases/tag/1.0.1
[1.1.0]: https://github.com/ergebnis/twig-front-matter/releases/tag/1.1.0
[1.2.0]: https://github.com/ergebnis/twig-front-matter/releases/tag/1.2.0
[1.3.0]: https://github.com/ergebnis/twig-front-matter/releases/tag/1.3.0

[7039e81...1.0.0]: https://github.com/ergebnis/twig-front-matter/compare/7039e81...1.0.0
[1.0.0...1.0.1]: https://github.com/ergebnis/twig-front-matter/compare/1.0.0...1.0.1
[1.0.1...1.1.0]: https://github.com/ergebnis/twig-front-matter/compare/1.0.1...1.1.0
[1.1.0...1.2.0]: https://github.com/ergebnis/twig-front-matter/compare/1.1.0...1.2.0
[1.2.0...1.3.0]: https://github.com/ergebnis/twig-front-matter/compare/1.2.0...1.3.0
[1.3.0...main]: https://github.com/ergebnis/twig-front-matter/compare/1.3.0...main

[#1]: https://github.com/ergebnis/twig-front-matter/pull/1
[#2]: https://github.com/ergebnis/twig-front-matter/pull/2
[#3]: https://github.com/ergebnis/twig-front-matter/pull/3
[#4]: https://github.com/ergebnis/twig-front-matter/pull/4
[#5]: https://github.com/ergebnis/twig-front-matter/pull/5
[#6]: https://github.com/ergebnis/twig-front-matter/pull/6
[#7]: https://github.com/ergebnis/twig-front-matter/pull/7
[#8]: https://github.com/ergebnis/twig-front-matter/pull/8
[#81]: https://github.com/ergebnis/twig-front-matter/pull/81
[#82]: https://github.com/ergebnis/twig-front-matter/pull/82
[#275]: https://github.com/ergebnis/twig-front-matter/pull/275
[#343]: https://github.com/ergebnis/twig-front-matter/pull/343

[@localheinz]: https://github.com/localheinz
