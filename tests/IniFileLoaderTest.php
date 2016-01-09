<?php
/*
 * Copyright (c) 2015 Matthew Loberg
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

namespace Mlo\FileLoader\Tests;

use Mlo\FileLoader\IniFileLoader;
use Symfony\Component\Config\FileLocator;

/**
 * @coversDefaultClass \Mlo\FileLoader\IniFileLoader
 */
class IniFileLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var IniFileLoader
     */
    private $iniFileLoader;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        $this->iniFileLoader = new IniFileLoader(new FileLocator());
    }

    /**
     * @covers ::supports
     */
    public function testSupports()
    {
        $this->assertFalse($this->iniFileLoader->supports(new \stdClass()));
        $this->assertTrue($this->iniFileLoader->supports('data.ini'));
        $this->assertFalse($this->iniFileLoader->supports('data.yml'));
    }

    /**
     * @covers ::load
     */
    public function testLoad()
    {
        $values = $this->iniFileLoader->load(__DIR__ . '/data/sample.ini');

        $this->assertInternalType('array', $values);
        $this->assertEquals('BIRD', $values['animal']);
        $this->assertEquals('http://git.php.net', $values['urls']['git']);
        $this->assertTrue(in_array('7.0', $values['phpversion']));
    }
}
