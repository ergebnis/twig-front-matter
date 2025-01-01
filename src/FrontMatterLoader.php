<?php

declare(strict_types=1);

/**
 * Copyright (c) 2023-2025 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/twig-front-matter
 */

namespace Ergebnis\Twig\FrontMatter;

use Ergebnis\FrontMatter;
use Twig\Loader;
use Twig\Source;

final class FrontMatterLoader implements Loader\LoaderInterface
{
    public function __construct(
        private readonly Loader\LoaderInterface $loader,
        private readonly FrontMatter\Parser $frontMatterParser,
        private readonly Converter\FrontMatterConverter $frontMatterConverter,
    ) {
    }

    public function exists(string $name): bool
    {
        return $this->loader->exists($name);
    }

    public function isFresh(
        string $name,
        int $time,
    ): bool {
        return $this->loader->isFresh(
            $name,
            $time,
        );
    }

    public function getCacheKey(string $name): string
    {
        return $this->loader->getCacheKey($name);
    }

    public function getSourceContext(string $name): Source
    {
        $source = $this->loader->getSourceContext($name);

        $parsed = $this->frontMatterParser->parse(FrontMatter\Content::fromString($source->getCode()));

        $bodyMatter = $parsed->bodyMatter()->content()->toString();

        if ($source->getCode() === $bodyMatter) {
            return $source;
        }

        $frontMatterConvertedToTwigAssignments = $this->frontMatterConverter->convert($parsed->frontMatter()->data()->toArray());

        $lines = \substr_count(
            $parsed->frontMatter()->content()->toString(),
            "\n",
        );

        $content = <<<TWIG
{$frontMatterConvertedToTwigAssignments}
{% line {$lines} %}
{$bodyMatter}
TWIG;

        return new Source(
            $content,
            $source->getName(),
            $source->getPath(),
        );
    }
}
