<?php
/*
 * Copyright (c) 2015 Matthew Loberg
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

namespace Mlo\FileLoader\Tests;

use Mlo\FileLoader\FileLoader;
use Mlo\FileLoader\IniFileLoader;
use Mlo\FileLoader\JsonFileLoader;
use Mlo\FileLoader\YamlFileLoader;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;

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
        $this->cacheDirectory = __DIR__ . DIRECTORY_SEPARATOR . 'cache';
        $this->dataDirectory  = __DIR__ . DIRECTORY_SEPARATOR . 'data';

        foreach (glob($this->cacheDirectory . '/*') as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }

        $this->loader = new FileLoader($this->cacheDirectory, null, [
            new IniFileLoader(),
            new JsonFileLoader(),
            new YamlFileLoader(),
        ]);
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

        $this->assertSame($this->loader, $this->loader->setDirectories(['foo', 'bar']));

        $this->assertCount(2, $this->loader->getDirectories());
        $this->assertTrue(in_array('foo', $this->loader->getDirectories()));

        $this->assertSame($this->loader, $this->loader->addDirectory($this->dataDirectory));
        $this->assertSame($this->loader, $this->loader->addDirectory('foo'));

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

        $this->assertSame($this->loader, $this->loader->setCacheDirectory('foobar'));

        $this->assertEquals('foobar', $this->loader->getCacheDirectory());
    }

    /**
     * @covers ::getLoaders
     * @covers ::addLoader
     */
    public function testGetAddLoaders()
    {
        $this->assertCount(3, $this->loader->getLoaders());
        $this->assertSame($this->loader, $this->loader->addLoader(new TestFileLoader()));
        $this->assertCount(4, $this->loader->getLoaders());
    }

    /**
     * @covers ::load
     */
    public function testLoadYaml()
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
    public function testLoadJson()
    {
        $this->loader->addDirectory($this->dataDirectory);

        $this->assertFalse(file_exists($this->cacheDirectory . '/data.json.php'));

        $values = $this->loader->load('data.json');

        $this->assertEquals('bar', $values['foo']);

        $this->assertTrue(file_exists($this->cacheDirectory . '/data.json.php'));
    }

    public function testLoadIni()
    {
        $this->loader->addDirectory($this->dataDirectory);

        $this->assertFalse(file_exists($this->cacheDirectory . '/sample.ini.php'));

        $values = $this->loader->load('sample.ini');

        $this->assertEquals('BIRD', $values['animal']);

        $this->assertTrue(file_exists($this->cacheDirectory . '/sample.ini.php'));
    }

    /**
     * @covers ::load
     */
    public function testLoadFromCache()
    {
        $cacheFile = $this->cacheDirectory . '/foo.yml.php';

        $this->assertFalse(file_exists($cacheFile));

        $this->loader->addDirectory($this->dataDirectory);
        $this->loader->load('foo.yml');

        $this->assertTrue(file_exists($cacheFile));

        $fileModTime = filemtime($cacheFile);

        sleep(1);

        $this->loader->load('foo.yml');

        $this->assertEquals($fileModTime, filemtime($cacheFile));
    }

    /**
     * @covers ::load
     */
    public function testLoadRefreshWillAlwaysPullFromFile()
    {
        $cacheFile = $this->cacheDirectory . '/foo.yml.php';

        $this->assertFalse(file_exists($cacheFile));

        $this->loader->addDirectory($this->dataDirectory);
        $this->loader->load('foo.yml');

        $this->assertTrue(file_exists($cacheFile));

        $fileModTime = filemtime($cacheFile);

        sleep(1);

        $this->loader->load('foo.yml', true);

        $this->assertGreaterThan($fileModTime, filemtime($cacheFile));
    }

    /**
     * @covers ::loadWithConfiguration
     */
    public function testLoadWithConfiguration()
    {
        $cacheFile = $this->cacheDirectory . '/config.yml.config.database.php';

        $this->assertFalse(file_exists($cacheFile));

        $this->loader->addDirectory($this->dataDirectory);
        $values = $this->loader->loadWithConfiguration('config.yml', new Config());

        $this->assertTrue(file_exists($cacheFile));

        $this->assertCount(2, $values['connections']);

        $this->assertEquals('mysql', $values['connections']['default']['driver']);
        $this->assertEquals('sqlite', $values['connections']['test']['driver']);

        $fileModTime = filemtime($cacheFile);

        sleep(1);

        $this->loader->loadWithConfiguration('config.yml', new Config());

        $this->assertEquals($fileModTime, filemtime($cacheFile));

        $this->loader->loadWithConfiguration('config.yml', new Config(), true);

        $this->assertGreaterThan($fileModTime, filemtime($cacheFile));
    }
}

class TestFileLoader implements LoaderInterface
{
    public function load($resource, $type = null)
    {
    }

    public function supports($resource, $type = null)
    {
    }

    public function getResolver()
    {
    }

    public function setResolver(LoaderResolverInterface $resolver)
    {
    }
}
