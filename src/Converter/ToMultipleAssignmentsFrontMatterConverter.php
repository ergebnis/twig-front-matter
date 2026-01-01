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

use Ergebnis\Twig\FrontMatter\Expression;

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
                $name = Expression\Name::fromString($key);
            } catch (\InvalidArgumentException) {
                continue;
            }

            $assignments[] = Expression\Assignment::create(
                $name,
                Expression\Value::fromRaw($value),
                $this->force,
            );
        }

        return \implode("\n", \array_map(static function (Expression\Assignment $assignment): string {
            return $assignment->toString();
        }, $assignments));
    }
}
