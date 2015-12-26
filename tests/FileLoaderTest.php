<?php
/*
 * Copyright (c) 2015 Matthew Loberg
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

namespace Mlo\FileLoader\Tests;

use Mlo\FileLoader\FileLoader;

/**
 * @coversDefaultClass \Mlo\FileLoader\FileLoader
 */
class FileLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    private $cacheDirectory;

    /**
     * @var string
     */
    private $dataDirectory;

    /**
     * @var FileLoader
     */
    private $loader;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->cacheDirectory = implode(DIRECTORY_SEPARATOR, [__DIR__, 'cache']);
        $this->dataDirectory = implode(DIRECTORY_SEPARATOR, [__DIR__, 'data']);

        foreach (glob($this->cacheDirectory . '/*') as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }

        $this->loader = new FileLoader($this->cacheDirectory);
    }

    /**
     * @covers ::__construct
     */
    public function testConstructor()
    {
        $loader = new FileLoader('foo', ['bar', 'baz']);

        $this->assertEquals('foo', $loader->getCacheDirectory());
        $this->assertCount(2, $loader->getDirectories());
        $this->assertTrue(in_array('bar', $loader->getDirectories()));
        $this->assertTrue(in_array('baz', $loader->getDirectories()));
    }

    /**
     * @covers ::getDirectories
     * @covers ::setDirectories
     * @covers ::addDirectory
     */
    public function testSetGetAddDirectories()
    {
        $this->assertCount(0, $this->loader->getDirectories());

        $this->loader->setDirectories(['foo', 'bar']);

        $this->assertCount(2, $this->loader->getDirectories());
        $this->assertTrue(in_array('foo', $this->loader->getDirectories()));

        $this->loader->addDirectory($this->dataDirectory);
        $this->loader->addDirectory('foo');

        $this->assertCount(3, $this->loader->getDirectories());
        $this->assertTrue(in_array($this->dataDirectory, $this->loader->getDirectories()));
    }

    /**
     * @covers ::getCacheDirectory
     * @covers ::setCacheDirectory
     */
    public function testSetGetCacheDirectory()
    {
        $this->assertEquals($this->cacheDirectory, $this->loader->getCacheDirectory());

        $this->loader->setCacheDirectory('foobar');

        $this->assertEquals('foobar', $this->loader->getCacheDirectory());
    }

    /**
     * @covers ::load
     */
    public function testLoad()
    {
        $this->loader->addDirectory($this->dataDirectory);

        $this->assertFalse(file_exists($this->cacheDirectory . '/test.yml.php'));

        $values = $this->loader->load('test.yml');

        $this->assertEquals('bar', $values['foo']);

        $this->assertTrue(file_exists($this->cacheDirectory . '/test.yml.php'));
    }

    /**
     * @covers ::load
     */
    public function testLoadFromCache()
    {
        $this->loader->addDirectory($this->dataDirectory);
        $this->loader->load('foo.yml');

        file_put_contents($this->cacheDirectory . '/foo.yml.php', '<?php return [ "bar" => "baz" ];');

        $values = $this->loader->load('foo.yml');

        $this->assertEquals('baz', $values['bar']);
    }

    /**
     * @covers ::load
     */
    public function testLoadRefreshWillAlwaysPullFromFile()
    {
        $this->loader->addDirectory($this->dataDirectory);
        $this->loader->load('foo.yml');

        file_put_contents($this->cacheDirectory . '/foo.yml.php', '<?php return [];');

        $this->assertCount(0, $this->loader->load('foo.yml'));

        $values = $this->loader->load('foo.yml', true);

        $this->assertEquals('foobar', $values['test']);
    }
}
