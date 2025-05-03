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

namespace Ergebnis\Twig\FrontMatter\Test\Unit;

use Ergebnis\DataProvider;
use Ergebnis\FrontMatter;
use Ergebnis\Twig\FrontMatter\Converter;
use Ergebnis\Twig\FrontMatter\Expression;
use Ergebnis\Twig\FrontMatter\FrontMatterLoader;
use Ergebnis\Twig\FrontMatter\Test;
use PHPUnit\Framework;
use Twig\Loader;
use Twig\Source;

#[Framework\Attributes\CoversClass(FrontMatterLoader::class)]
#[Framework\Attributes\UsesClass(Expression\Assignment::class)]
#[Framework\Attributes\UsesClass(Expression\Name::class)]
#[Framework\Attributes\UsesClass(Expression\Value::class)]
#[Framework\Attributes\UsesClass(Converter\ToMultipleAssignmentsFrontMatterConverter::class)]
final class FrontMatterLoaderTest extends Framework\TestCase
{
    use Test\Util\Helper;

    #[Framework\Attributes\DataProviderExternal(DataProvider\BoolProvider::class, 'arbitrary')]
    public function testExistsReturnsExistsFromOriginalLoader(bool $exists): void
    {
        $name = self::faker()->word();

        $originalLoader = $this->createMock(Loader\LoaderInterface::class);

        $originalLoader
            ->method('exists')
            ->with(self::identicalTo($name))
            ->willReturn($exists);

        $frontMatterLoader = new FrontMatterLoader(
            $originalLoader,
            self::createStub(FrontMatter\Parser::class),
            self::createStub(Converter\FrontMatterConverter::class),
        );

        self::assertSame($exists, $frontMatterLoader->exists($name));
    }

    public function testGetCacheKeyReturnsCacheKeyFromOriginalLoader(): void
    {
        $faker = self::faker();

        $name = $faker->word();
        $cacheKey = $faker->sha1();

        $originalLoader = $this->createMock(Loader\LoaderInterface::class);

        $originalLoader
            ->method('getCacheKey')
            ->with(self::identicalTo($name))
            ->willReturn($cacheKey);

        $frontMatterLoader = new FrontMatterLoader(
            $originalLoader,
            self::createStub(FrontMatter\Parser::class),
            self::createStub(Converter\FrontMatterConverter::class),
        );

        self::assertSame($cacheKey, $frontMatterLoader->getCacheKey($name));
    }

    #[Framework\Attributes\DataProviderExternal(DataProvider\BoolProvider::class, 'arbitrary')]
    public function testIsFreshReturnsIsFreshFromOriginalLoader(bool $isFresh): void
    {
        $faker = self::faker();

        $name = $faker->word();
        $time = $faker->numberBetween();

        $originalLoader = $this->createMock(Loader\LoaderInterface::class);

        $originalLoader
            ->method('isFresh')
            ->with(
                self::identicalTo($name),
                self::identicalTo($time),
            )
            ->willReturn($isFresh);

        $frontMatterLoader = new FrontMatterLoader(
            $originalLoader,
            self::createStub(FrontMatter\Parser::class),
            self::createStub(Converter\FrontMatterConverter::class),
        );

        self::assertSame($isFresh, $frontMatterLoader->isFresh($name, $time));
    }

    public function testGetSourceContextReturnsSourceWhenOriginalSourceCodeDoesNotHaveFrontMatter(): void
    {
        $name = self::faker()->word();

        $originalSource = new Source(
            <<<'TWIG'
{{ foo }}
TWIG
            ,
            $name,
        );

        $originalLoader = $this->createMock(Loader\LoaderInterface::class);

        $originalLoader
            ->method('getSourceContext')
            ->with(self::identicalTo($name))
            ->willReturn($originalSource);

        $frontMatterLoader = new FrontMatterLoader(
            $originalLoader,
            new FrontMatter\YamlParser(),
            self::createStub(Converter\FrontMatterConverter::class),
        );

        self::assertSame($originalSource, $frontMatterLoader->getSourceContext($name));
    }

    public function testGetSourceContextReturnsSourceWhenOriginalSourceCodeHasFrontMatter(): void
    {
        $faker = self::faker();

        $name = $faker->word();
        $path = $faker->slug();

        $originalSource = new Source(
            <<<'TWIG'
---
foo: bar
baz:
  - qux
  - quz
---
{{ foo }}
TWIG
            ,
            $name,
            $path,
        );

        $originalLoader = $this->createMock(Loader\LoaderInterface::class);

        $originalLoader
            ->method('getSourceContext')
            ->with(self::identicalTo($name))
            ->willReturn($originalSource);

        $frontMatterLoader = new FrontMatterLoader(
            $originalLoader,
            new FrontMatter\YamlParser(),
            new Converter\ToMultipleAssignmentsFrontMatterConverter(false),
        );

        $source = $frontMatterLoader->getSourceContext($name);

        $code = <<<'TWIG'
{% set foo = foo is defined ? foo : "bar" %}
{% set baz = baz is defined ? baz|merge({ 0: "qux", 1: "quz" }) : { 0: "qux", 1: "quz" } %}
{% line 6 %}
{{ foo }}
TWIG;

        self::assertSame($code, $source->getCode());
        self::assertSame($name, $source->getName());
        self::assertSame($path, $source->getPath());
    }
}
