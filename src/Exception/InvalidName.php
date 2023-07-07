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

namespace Ergebnis\Twig\FrontMatter\Exception;

final class InvalidName extends \InvalidArgumentException
{
    public static function invalid(string $value): self
    {
        return new self(\sprintf(
            'Value "%s" can not be used as value for a parameter name.',
            $value,
        ));
    }
}
