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

namespace Ergebnis\Twig\FrontMatter\Converter;

use Ergebnis\Twig\FrontMatter;

final class ToMultipleAssignmentsFrontMatterConverter implements FrontMatterConverter
{
    public function __construct(private readonly bool $force)
    {
    }

    public function convert(array $data): string
    {
        $assignments = [];

        foreach ($data as $key => $value) {
            try {
                $name = FrontMatter\Expression\Name::fromString($key);
            } catch (\InvalidArgumentException) {
                continue;
            }

            $assignments[] = FrontMatter\Expression\Assignment::create(
                $name,
                FrontMatter\Expression\Value::fromRaw($value),
                $this->force,
            );
        }

        return \implode("\n", \array_map(static function (FrontMatter\Expression\Assignment $assignment): string {
            return $assignment->toString();
        }, $assignments));
    }
}
