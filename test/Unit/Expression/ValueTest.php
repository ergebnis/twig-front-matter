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

namespace Ergebnis\Twig\FrontMatter\Test\Unit\Expression;

use Ergebnis\Twig\FrontMatter\Expression;
use Ergebnis\Twig\FrontMatter\Test;
use PHPUnit\Framework;

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
            \JSON_UNESCAPED_UNICODE,
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
