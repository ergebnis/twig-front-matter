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

final class ToSingleAssignmentFrontMatterConverter implements FrontMatterConverter
{
    public function __construct(
        private readonly FrontMatter\Expression\Name $name,
        private readonly bool $force,
    ) {
    }

    public function convert(array $data): string
    {
        $assignment = FrontMatter\Expression\Assignment::create(
            $this->name,
            FrontMatter\Expression\Value::fromRaw($data),
            $this->force,
        );

        return $assignment->toString();
    }
}
