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

namespace Ergebnis\Twig\FrontMatter\Expression;

/**
 * @see https://twig.symfony.com/doc/3.x/tags/set.html
 */
final class Assignment
{
    private function __construct(
        private readonly Name $name,
        private readonly Value $value,
        private readonly bool $force,
    ) {
    }

    public static function create(
        Name $name,
        Value $value,
        bool $force,
    ): self {
        return new self(
            $name,
            $value,
            $force,
        );
    }

    public function toString(): string
    {
        $name = $this->name->toString();
        $value = $this->value->toString();

        if ($this->force) {
            return <<<TWIG
{% set {$name} = {$value} %}
TWIG;
        }

        if ($this->value->isMergeable()) {
            return <<<TWIG
{% set {$name} = {$name} is defined ? {$name}|merge({$value}) : {$value} %}
TWIG;
        }

        return <<<TWIG
{% set {$name} = {$name} is defined ? {$name} : {$value} %}
TWIG;
    }
}
