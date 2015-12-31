<?php
/*
 * Copyright (c) 2015 Matthew Loberg
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

namespace Mlo\FileLoader\Tests;

use Mlo\FileLoader\JsonFileLoader;
use Symfony\Component\Config\FileLocator;

/**
 * @coversDefaultClass \Mlo\FileLoader\JsonFileLoader
 */
class JsonFileLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var JsonFileLoader
     */
    private $jsonFileLoader;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        $this->jsonFileLoader = new JsonFileLoader(new FileLocator());
    }

    /**
     * @covers ::supports
     */
    public function testSupports()
    {
        $this->assertFalse($this->jsonFileLoader->supports(new \stdClass()));
        $this->assertTrue($this->jsonFileLoader->supports('data.json'));
        $this->assertFalse($this->jsonFileLoader->supports('data.yml'));
    }

    /**
     * @covers ::load
     */
    public function testLoad()
    {
        $values = $this->jsonFileLoader->load(__DIR__ . '/data/data.json');

        $this->assertInternalType('array', $values);
        $this->assertEquals('bar', $values['foo']);
        $this->assertEquals('world', $values['name']);
        $this->assertEquals('hello', $values['greeting']);
    }
}
