<?php

declare(strict_types=1);

/**
 * Copyright (c) 2023-2026 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/twig-front-matter
 */

namespace Ergebnis\Twig\FrontMatter\Test\Unit\Converter;

use Ergebnis\Twig\FrontMatter\Converter;
use Ergebnis\Twig\FrontMatter\Exception;
use Ergebnis\Twig\FrontMatter\Expression;
use Ergebnis\Twig\FrontMatter\Test;
use PHPUnit\Framework;
use Symfony\Component\Yaml;

#[Framework\Attributes\CoversClass(Converter\ToMultipleAssignmentsFrontMatterConverter::class)]
#[Framework\Attributes\UsesClass(Expression\Assignment::class)]
#[Framework\Attributes\UsesClass(Expression\Name::class)]
#[Framework\Attributes\UsesClass(Expression\Value::class)]
#[Framework\Attributes\UsesClass(Exception\InvalidName::class)]
final class ToMultipleAssignmentsFrontMatterConverterTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testConvertReturnsStringWithMultipleTwigAssignmentsWhenForceIsTrue(): void
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

        $frontMatterConverter = new Converter\ToMultipleAssignmentsFrontMatterConverter(true);

        $expected = <<<'TWIG'
{% set foo = "bar" %}
{% set number = 1234 %}
{% set pi = 3.14159 %}
{% set date = (1464307200|date_modify('0sec')) %}
{% set empty = null %}
{% set multiline = "Multiple\nLine\nString\n" %}
{% set object = { key: "value", datetime: (1605185652|date_modify('0sec')), values: { 0: "one", 1: "two" } } %}
TWIG;

        self::assertSame($expected, $frontMatterConverter->convert($data));
    }

    public function testConvertReturnsStringWithMultipleTwigAssignmentsWhenForceIsFalse(): void
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

        $frontMatterConverter = new Converter\ToMultipleAssignmentsFrontMatterConverter(false);

        $expected = <<<'TWIG'
{% set foo = foo is defined ? foo : "bar" %}
{% set number = number is defined ? number : 1234 %}
{% set pi = pi is defined ? pi : 3.14159 %}
{% set date = date is defined ? date : (1464307200|date_modify('0sec')) %}
{% set empty = empty is defined ? empty : null %}
{% set multiline = multiline is defined ? multiline : "Multiple\nLine\nString\n" %}
{% set object = object is defined ? object|merge({ key: "value", datetime: (1605185652|date_modify('0sec')), values: { 0: "one", 1: "two" } }) : { key: "value", datetime: (1605185652|date_modify('0sec')), values: { 0: "one", 1: "two" } } %}
TWIG;

        self::assertSame($expected, $frontMatterConverter->convert($data));
    }
}
