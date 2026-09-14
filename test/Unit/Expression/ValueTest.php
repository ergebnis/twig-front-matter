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

namespace Ergebnis\Twig\FrontMatter\Test\Unit\Expression;

use Ergebnis\Twig\FrontMatter\Expression;
use Ergebnis\Twig\FrontMatter\Test;
use PHPUnit\Framework;
use Twig\Environment;
use Twig\Loader;
use Twig\Source;

#[Framework\Attributes\CoversClass(Expression\Value::class)]
final class ValueTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testFromRawReturnsValueWhenRawIsNull(): void
    {
        $value = Expression\Value::fromRaw(null);

        self::assertSame('null', $value->toString());
        self::assertFalse($value->isMergeable());
    }

    public function testFromRawReturnsValueWhenRawIsFalse(): void
    {
        $value = Expression\Value::fromRaw(false);

        self::assertSame('false', $value->toString());
        self::assertFalse($value->isMergeable());
    }

    public function testFromRawReturnsValueWhenRawIsTrue(): void
    {
        $value = Expression\Value::fromRaw(true);

        self::assertSame('true', $value->toString());
        self::assertFalse($value->isMergeable());
    }

    public function testFromRawReturnsValueWhenRawIsInt(): void
    {
        $raw = self::faker()->numberBetween();

        $value = Expression\Value::fromRaw($raw);

        self::assertSame((string) $raw, $value->toString());
        self::assertFalse($value->isMergeable());
    }

    public function testFromRawReturnsValueWhenRawIsFloat(): void
    {
        $raw = self::faker()->randomFloat(5);

        $value = Expression\Value::fromRaw($raw);

        self::assertSame((string) $raw, $value->toString());
        self::assertFalse($value->isMergeable());
    }

    public function testFromRawReturnsValueWhenRawIsArray(): void
    {
        $raw = [
            'bool-false' => false,
            'bool-true' => true,
            'dateTime' => new \DateTimeImmutable('2021-01-16 17:12:51'),
            'float' => 3.14159,
            'int' => 9000,
            'null' => null,
            'string' => 'foo',
        ];

        $value = Expression\Value::fromRaw($raw);

        $expected = <<<'TWIG'
{ bool-false: false, bool-true: true, dateTime: (1610817171|date_modify('0sec')), float: 3.14159, int: 9000, null: null, string: "foo" }
TWIG;

        self::assertSame($expected, $value->toString());
        self::assertTrue($value->isMergeable());
    }

    public function testFromRawReturnsValueWhenRawIsDateTime(): void
    {
        $raw = self::faker()->dateTime();

        $value = Expression\Value::fromRaw($raw);

        $expected = <<<TWIG
({$raw->getTimestamp()}|date_modify('0sec'))
TWIG;

        self::assertSame($expected, $value->toString());
        self::assertFalse($value->isMergeable());
    }

    public function testFromRawReturnsValueWhenRawIsDateTimeImmutable(): void
    {
        $raw = \DateTimeImmutable::createFromMutable(self::faker()->dateTime());

        $value = Expression\Value::fromRaw($raw);

        $expected = <<<TWIG
({$raw->getTimestamp()}|date_modify('0sec'))
TWIG;

        self::assertSame($expected, $value->toString());
        self::assertFalse($value->isMergeable());
    }

    public function testFromRawReturnsValueWhenRawIsObject(): void
    {
        $raw = (object) [
            'bool-false' => false,
            'bool-true' => true,
            'dateTime' => new \DateTimeImmutable('2021-01-16 17:12:51'),
            'float' => 3.14159,
            'int' => 9000,
            'null' => null,
            'string' => 'foo',
            'string-with-umlaut' => 'My name is Andreas Möller, and I am a self-employed Software Engineer and Consultant from Berlin, Germany. What can I do for you?',
        ];

        $value = Expression\Value::fromRaw($raw);

        $expected = \json_encode(
            $raw,
            \JSON_UNESCAPED_LINE_TERMINATORS | \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE,
        );

        self::assertSame($expected, $value->toString());
        self::assertTrue($value->isMergeable());
    }

    public function testFromRawReturnsValueWhenRawIsString(): void
    {
        $raw = self::faker()->sentence();

        $value = Expression\Value::fromRaw($raw);

        $expected = <<<TXT
"{$raw}"
TXT;

        self::assertSame($expected, $value->toString());
        self::assertFalse($value->isMergeable());
    }

    public function testFromRawReturnsValueWhenRawIsStringWithSlashes(): void
    {
        $faker = self::faker();

        $raw = \sprintf(
            '%s/%s/%s',
            $faker->word(),
            $faker->word(),
            $faker->word(),
        );

        $value = Expression\Value::fromRaw($raw);

        $expected = <<<TXT
"{$raw}"
TXT;

        self::assertSame($expected, $value->toString());
        self::assertFalse($value->isMergeable());
    }

    public function testFromRawReturnsValueThatCompilesWithoutDeprecationsWhenRawIsStringWithSlashes(): void
    {
        $faker = self::faker();

        $raw = \sprintf(
            '%s/%s/%s',
            $faker->word(),
            $faker->word(),
            $faker->word(),
        );

        $value = Expression\Value::fromRaw($raw);

        $source = new Source(
            <<<TWIG
{% set foo = {$value->toString()} %}
TWIG,
            'template.html.twig',
        );

        $environment = new Environment(new Loader\ArrayLoader());

        $deprecations = [];

        \set_error_handler(
            static function (
                int $level,
                string $message,
            ) use (&$deprecations): bool {
                $deprecations[] = $message;

                return true;
            },
            \E_USER_DEPRECATED,
        );

        try {
            $environment->parse($environment->tokenize($source));
        } finally {
            \restore_error_handler();
        }

        self::assertSame([], $deprecations);
    }

    #[Framework\Attributes\DataProvider('provideStringWithCharactersThatJsonEncodesWithEscapeSequencesNotSupportedByTwig')]
    public function testFromRawReturnsValueThatRendersWithoutDeprecationsWhenRawIsStringWithCharactersThatJsonEncodesWithEscapeSequencesNotSupportedByTwig(string $raw): void
    {
        $value = Expression\Value::fromRaw($raw);

        $environment = new Environment(
            new Loader\ArrayLoader([
                'template.html.twig' => <<<TWIG
{% set foo = {$value->toString()} %}{{ foo }}
TWIG,
            ]),
            [
                'autoescape' => false,
            ],
        );

        $deprecations = [];

        \set_error_handler(
            static function (
                int $level,
                string $message,
            ) use (&$deprecations): bool {
                $deprecations[] = $message;

                return true;
            },
            \E_USER_DEPRECATED,
        );

        try {
            $rendered = $environment->render('template.html.twig');
        } finally {
            \restore_error_handler();
        }

        self::assertSame($raw, $rendered);
        self::assertSame([], $deprecations);
    }

    /**
     * @return \Generator<string, array{0: string}>
     */
    public static function provideStringWithCharactersThatJsonEncodesWithEscapeSequencesNotSupportedByTwig(): iterable
    {
        $faker = self::faker();

        $characters = [
            'backslash-followed-by-b' => '\b',
            'backslash-followed-by-backslash-followed-by-u' => \sprintf(
                '%s%su0041',
                '\\',
                '\\',
            ),
            'backslash-followed-by-u' => \sprintf(
                '%su0041',
                '\\',
            ),
            'line-separator' => "\u{2028}",
            'paragraph-separator' => "\u{2029}",
        ];

        foreach (\range(0x00, 0x1F) as $codePoint) {
            $characters[\sprintf('control-character-%02x', $codePoint)] = \chr($codePoint);
        }

        foreach ($characters as $key => $character) {
            yield $key => [
                \sprintf(
                    '%s%s%s',
                    $faker->word(),
                    $character,
                    $faker->word(),
                ),
            ];
        }
    }

    public function testFromRawReturnsValueThatRendersWithoutInterpolationWhenRawIsStringWithInterpolation(): void
    {
        $faker = self::faker();

        $name = \sprintf(
            '%sVariable',
            $faker->word(),
        );

        $raw = \sprintf(
            '%s#{%s}%s',
            $faker->word(),
            $name,
            $faker->word(),
        );

        $value = Expression\Value::fromRaw($raw);

        $environment = new Environment(
            new Loader\ArrayLoader([
                'template.html.twig' => <<<TWIG
{% set foo = {$value->toString()} %}{{ foo }}
TWIG,
            ]),
            [
                'autoescape' => false,
                'strict_variables' => true,
            ],
        );

        $rendered = $environment->render('template.html.twig', [
            $name => $faker->sentence(),
        ]);

        self::assertSame($raw, $rendered);
    }

    public function testFromRawReturnsValueWhenRawIsStringWithUmlauts(): void
    {
        $raw = 'My name is Andreas Möller, and I am a self-employed Software Engineer and Consultant from Berlin, Germany. What can I do for you?';

        $value = Expression\Value::fromRaw($raw);

        $expected = <<<TXT
"{$raw}"
TXT;

        self::assertSame($expected, $value->toString());
        self::assertFalse($value->isMergeable());
    }
}
