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

namespace Ergebnis\Twig\FrontMatter\Converter;

final class NullFrontMatterConverter implements FrontMatterConverter
{
    public function convert(array $data): string
    {
        return '';
    }
}
