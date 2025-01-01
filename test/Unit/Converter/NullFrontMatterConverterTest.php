<?php

declare(strict_types=1);

/**
 * Copyright (c) 2023-2025 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/twig-front-matter
 */

namespace Ergebnis\Twig\FrontMatter\Test\Unit\Converter;

use Ergebnis\Twig\FrontMatter\Converter;
use PHPUnit\Framework;
use Symfony\Component\Yaml;

#[Framework\Attributes\CoversClass(Converter\NullFrontMatterConverter::class)]
final class NullFrontMatterConverterTest extends Framework\TestCase
{
    public function testConvertReturnsEmptyString(): void
    {
        $yaml = <<<'YAML'
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
YAML;

        $data = Yaml\Yaml::parse(
            $yaml,
            Yaml\Yaml::PARSE_DATETIME,
        );

        $frontMatterConverter = new Converter\NullFrontMatterConverter();

        self::assertSame('', $frontMatterConverter->convert($data));
    }
}
