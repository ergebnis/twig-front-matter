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

final class Value
{
    /**
     * @param array|\DateTimeInterface|mixed $raw
     */
    private function __construct(private readonly mixed $raw)
    {
    }

    /**
     * @param array|\DateTimeInterface|mixed $raw
     */
    public static function fromRaw(mixed $raw): self
    {
        return new self($raw);
    }

    public function toString(): string
    {
        /**
         * @see https://twig.symfony.com/doc/3.x/filters/date_modify.html
         */
        if ($this->raw instanceof \DateTimeInterface) {
            return <<<TWIG
({$this->raw->getTimestamp()}|date_modify('0sec'))
TWIG;
        }

        if (\is_array($this->raw)) {
            $keyValuePairs = [];

            foreach ($this->raw as $name => $raw) {
                $value = self::fromRaw($raw);

                $keyValuePairs[] = <<<TWIG
{$name}: {$value->toString()}
TWIG;
            }

            $twig = \implode(
                ', ',
                $keyValuePairs,
            );

            return <<<TWIG
{ {$twig} }
TWIG;
        }

        return \json_encode(
            $this->raw,
            \JSON_UNESCAPED_UNICODE,
        );
    }

    /**
     * @see https://twig.symfony.com/doc/3.x/filters/merge.html
     */
    public function isMergeable(): bool
    {
        if (null === $this->raw) {
            return false;
        }

        if ($this->raw instanceof \DateTimeInterface) {
            return false;
        }

        if (\is_scalar($this->raw)) {
            return false;
        }

        return true;
    }
}
