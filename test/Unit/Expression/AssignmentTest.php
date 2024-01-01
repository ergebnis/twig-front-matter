<?php

declare(strict_types=1);

/**
 * Copyright (c) 2023-2024 Andreas Möller
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

#[Framework\Attributes\CoversClass(Expression\Assignment::class)]
#[Framework\Attributes\UsesClass(Expression\Name::class)]
#[Framework\Attributes\UsesClass(Expression\Value::class)]
final class AssignmentTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testCreateReturnsAssignmentWhenForceIsTrueAndValueIsMergeable(): void
    {
        $faker = self::faker();

        $name = Expression\Name::fromString($faker->word());
        $value = Expression\Value::fromRaw([
            'words' => $faker->words(),
            'dateTime' => $faker->dateTime(),
            'bool' => $faker->boolean(),
        ]);

        $assignment = Expression\Assignment::create(
            $name,
            $value,
            true,
        );

        $expected = <<<TWIG
{% set {$name->toString()} = {$value->toString()} %}
TWIG;

        self::assertSame($expected, $assignment->toString());
    }

    public function testCreateReturnsAssignmentWhenForceIsTrueAndValueIsNotMergeable(): void
    {
        $faker = self::faker();

        $name = Expression\Name::fromString($faker->word());
        $value = Expression\Value::fromRaw($faker->sentence());

        $assignment = Expression\Assignment::create(
            $name,
            $value,
            true,
        );

        $expected = <<<TWIG
{% set {$name->toString()} = {$value->toString()} %}
TWIG;

        self::assertSame($expected, $assignment->toString());
    }

    public function testCreateReturnsAssignmentWhenForceIsFalseAndValueIsMergeable(): void
    {
        $faker = self::faker();

        $name = Expression\Name::fromString($faker->word());
        $value = Expression\Value::fromRaw([
            'words' => $faker->words(),
            'dateTime' => $faker->dateTime(),
            'bool' => $faker->boolean(),
        ]);

        $assignment = Expression\Assignment::create(
            $name,
            $value,
            false,
        );

        $expected = <<<TWIG
{% set {$name->toString()} = {$name->toString()} is defined ? {$name->toString()}|merge({$value->toString()}) : {$value->toString()} %}
TWIG;

        self::assertSame($expected, $assignment->toString());
    }

    public function testCreateReturnsAssignmentWhenForceIsFalseAndValueIsNotMergeable(): void
    {
        $faker = self::faker();

        $name = Expression\Name::fromString($faker->word());
        $value = Expression\Value::fromRaw($faker->sentence());

        $assignment = Expression\Assignment::create(
            $name,
            $value,
            false,
        );

        $expected = <<<TWIG
{% set {$name->toString()} = {$name->toString()} is defined ? {$name->toString()} : {$value->toString()} %}
TWIG;

        self::assertSame($expected, $assignment->toString());
    }
}
