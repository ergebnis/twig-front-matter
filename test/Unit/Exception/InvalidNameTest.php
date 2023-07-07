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

namespace Ergebnis\Twig\FrontMatter\Test\Unit\Exception;

use Ergebnis\Twig\FrontMatter\Exception;
use Ergebnis\Twig\FrontMatter\Test;
use PHPUnit\Framework;

#[Framework\Attributes\CoversClass(Exception\InvalidName::class)]
final class InvalidNameTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testInvalidReturnsException(): void
    {
        $value = self::faker()->word();

        $exception = Exception\InvalidName::invalid($value);

        $expected = \sprintf(
            'Value "%s" can not be used as value for a parameter name.',
            $value,
        );

        self::assertSame($expected, $exception->getMessage());
    }
}
