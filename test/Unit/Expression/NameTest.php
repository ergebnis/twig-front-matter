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

use Ergebnis\DataProvider;
use Ergebnis\Twig\FrontMatter\Exception;
use Ergebnis\Twig\FrontMatter\Expression;
use Ergebnis\Twig\FrontMatter\Test;
use PHPUnit\Framework;

#[Framework\Attributes\CoversClass(Expression\Name::class)]
#[Framework\Attributes\UsesClass(Exception\InvalidName::class)]
final class NameTest extends Framework\TestCase
{
    use Test\Util\Helper;

    #[Framework\Attributes\DataProvider('provideInvalidValue')]
    #[Framework\Attributes\DataProviderExternal(DataProvider\StringProvider::class, 'blank')]
    #[Framework\Attributes\DataProviderExternal(DataProvider\StringProvider::class, 'empty')]
    public function testFromStringRejectsInvalidValue(string $value): void
    {
        $this->expectException(Exception\InvalidName::class);

        Expression\Name::fromString($value);
    }

    /**
     * @return \Generator<string, array{0: string}>
     */
    public static function provideInvalidValue(): iterable
    {
        foreach (self::invalidCharacters() as $name => $character) {
            $key = \sprintf(
                'string-%s',
                $name,
            );

            yield $key => [
                $character,
            ];
        }

        foreach (self::invalidCharacters() as $name => $character) {
            $key = \sprintf(
                'string-%s-at-start',
                $name,
            );

            yield $key => [
                \sprintf(
                    '%sfoo',
                    $character,
                ),
            ];
        }

        foreach (self::invalidCharacters() as $name => $character) {
            $key = \sprintf(
                'string-%s-at-end',
                $name,
            );

            yield $key => [
                \sprintf(
                    'foo%s',
                    $character,
                ),
            ];
        }

        foreach (self::invalidCharacters() as $name => $character) {
            $name = \sprintf(
                'string-%s-at-middle',
                $name,
            );

            yield $name => [
                \sprintf(
                    'foo%sbar',
                    $character,
                ),
            ];
        }
    }

    #[Framework\Attributes\DataProvider('provideValidValue')]
    public function testFromStringReturnsNameWhenValueIsValid(string $value): void
    {
        $name = Expression\Name::fromString($value);

        self::assertSame($value, $name->toString());
    }

    /**
     * @return \Generator<string, array{0: string}>
     */
    public static function provideValidValue(): iterable
    {
        $values = [
            'contains-number' => 'foo9000bar',
            'contains-underscore' => 'foo_bar',
            'ends-with-number' => 'foo9000',
            'word-case-lower' => 'foo',
            'word-case-mixed' => 'FoO',
            'word-case-upper' => 'FOO',
        ];

        foreach ($values as $key => $value) {
            yield $key => [
                $value,
            ];
        }
    }

    /**
     * @return array<string, string>
     */
    private static function invalidCharacters(): array
    {
        return [
            'backslash' => '\\',
            'colon' => ':',
            'dash' => '-',
            'dollar' => '$',
            'dot' => '.',
            'slash' => '/',
            'space' => ' ',
        ];
    }
}
