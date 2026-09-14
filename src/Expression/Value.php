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
         * Twig has no literals for infinite and not-a-number floats, and json_encode() can not encode them, so use the corresponding constants.
         *
         * @see https://twig.symfony.com/doc/3.x/functions/constant.html
         */
        if (\is_float($this->raw)) {
            if (\is_nan($this->raw)) {
                return "constant('NAN')";
            }

            if (\is_infinite($this->raw)) {
                if (-\INF === $this->raw) {
                    return "(-constant('INF'))";
                }

                return "constant('INF')";
            }
        }

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

            foreach ($this->raw as $key => $raw) {
                /**
                 * Twig only accepts names and numbers as unquoted keys, so quote string keys.
                 *
                 * @see https://twig.symfony.com/doc/3.x/templates.html#literals
                 */
                $name = $key;

                if (\is_string($key)) {
                    $name = self::fromRaw($key)->toString();
                }

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

        $json = \json_encode(
            $this->raw,
            \JSON_UNESCAPED_LINE_TERMINATORS | \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE,
        );

        /**
         * Twig does not support the \b and \u escape sequences, so replace them with the equivalent \x escape sequences.
         *
         * Twig interpolates #{ in double-quoted strings, so escape it.
         *
         * @see https://twig.symfony.com/doc/3.x/templates.html#literals
         * @see https://twig.symfony.com/doc/3.x/templates.html#string-interpolation
         */
        return \preg_replace(
            [
                '/(?<!\\\\)((?:\\\\\\\\)*)\\\\b/',
                '/(?<!\\\\)((?:\\\\\\\\)*)\\\\u00([0-9a-f]{2})/',
                '/#\{/',
            ],
            [
                '$1\\\\x08',
                '$1\\\\x$2',
                '\\\\#{',
            ],
            $json,
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
