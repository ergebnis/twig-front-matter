<?php

declare(strict_types=1);

/**
 * Copyright (c) 2023 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/twig-front-matter
 */

namespace Ergebnis\Twig\FrontMatter\Test\Unit\Converter;

use Ergebnis\Twig\FrontMatter\Converter;
use Ergebnis\Twig\FrontMatter\Expression;
use Ergebnis\Twig\FrontMatter\Test;
use PHPUnit\Framework;
use Symfony\Component\Yaml;

#[Framework\Attributes\CoversClass(Converter\ToSingleAssignmentFrontMatterConverter::class)]
#[Framework\Attributes\UsesClass(Expression\Assignment::class)]
#[Framework\Attributes\UsesClass(Expression\Name::class)]
#[Framework\Attributes\UsesClass(Expression\Value::class)]
final class ToSingleAssignmentFrontMatterConverterTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testConvertReturnsStringWithSingleTwigAssignmentWhenForceIsTrue(): void
    {
        $name = self::faker()->word();

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

        $frontMatterConverter = new Converter\ToSingleAssignmentFrontMatterConverter(
            Expression\Name::fromString($name),
            true,
        );

        $expected = <<<TWIG
{% set {$name} = { foo: "bar", number: 1234, pi: 3.14159, date: (1464307200|date_modify('0sec')), empty: null, invalid-key: "hmm", multiline: "Multiple\\nLine\\nString\\n", object: { key: "value", datetime: (1605185652|date_modify('0sec')), values: { 0: "one", 1: "two" } } } %}
TWIG;

        self::assertSame($expected, $frontMatterConverter->convert($data));
    }

    public function testConvertReturnsStringWithSingleTwigAssignmentWhenForceIsFalse(): void
    {
        $name = self::faker()->word();

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

        $frontMatterConverter = new Converter\ToSingleAssignmentFrontMatterConverter(
            Expression\Name::fromString($name),
            false,
        );

        $expected = <<<TWIG
{% set {$name} = {$name} is defined ? {$name}|merge({ foo: "bar", number: 1234, pi: 3.14159, date: (1464307200|date_modify('0sec')), empty: null, invalid-key: "hmm", multiline: "Multiple\\nLine\\nString\\n", object: { key: "value", datetime: (1605185652|date_modify('0sec')), values: { 0: "one", 1: "two" } } }) : { foo: "bar", number: 1234, pi: 3.14159, date: (1464307200|date_modify('0sec')), empty: null, invalid-key: "hmm", multiline: "Multiple\\nLine\\nString\\n", object: { key: "value", datetime: (1605185652|date_modify('0sec')), values: { 0: "one", 1: "two" } } } %}
TWIG;

        self::assertSame($expected, $frontMatterConverter->convert($data));
    }
}
