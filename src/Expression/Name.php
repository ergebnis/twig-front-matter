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

namespace Ergebnis\Twig\FrontMatter\Expression;

use Ergebnis\Twig\FrontMatter\Exception;

final class Name
{
    private function __construct(private readonly string $value)
    {
    }

    /**
     * @throws Exception\InvalidName
     */
    public static function fromString(string $value): self
    {
        if (1 !== \preg_match('/^[a-z][0-9a-z_]*$/i', $value)) {
            throw Exception\InvalidName::invalid($value);
        }

        return new self($value);
    }

    public function toString(): string
    {
        return $this->value;
    }
}
