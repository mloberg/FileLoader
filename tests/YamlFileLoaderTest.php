<?php
/*
 * Copyright (c) 2015 Matthew Loberg
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

namespace Mlo\FileLoader\Tests;

use Mlo\FileLoader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;

/**
 * YamlFileLoaderTest
 *
 * @author Matthew Loberg <mloberg@nerdery.com>
 */
class YamlFileLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var YamlFileLoader
     */
    private $yamlFileLoader;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->yamlFileLoader = new YamlFileLoader(new FileLocator());
    }

    /**
     * @covers YamlFileLoader::supports
     */
    public function testSupports()
    {
        $this->assertFalse($this->yamlFileLoader->supports(new \stdClass()));
        $this->assertTrue($this->yamlFileLoader->supports('data.yml'));
        $this->assertTrue($this->yamlFileLoader->supports('data.yaml'));
        $this->assertFalse($this->yamlFileLoader->supports('data.json'));
    }

    /**
     * @covers YamlFileLoader::load
     */
    public function testLoad()
    {
        $values = $this->yamlFileLoader->load(__DIR__ . '/data/test.yml');

        $this->assertInternalType('array', $values);
        $this->assertEquals('bar', $values['foo']);
        $this->assertEquals('world', $values['hello']);
    }
}
