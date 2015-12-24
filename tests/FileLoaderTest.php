<?php
/*
 * Copyright (c) 2015 Matthew Loberg
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

namespace Mlo\FileLoader\Tests;

use Mlo\FileLoader\FileLoader;

/**
 * FileLoaderTest
 *
 * @author Matthew Loberg <mloberg@nerdery.com>
 */
class FileLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Clear all cache files
     */
    protected function setUp()
    {
        foreach (glob(__DIR__.DIRECTORY_SEPARATOR.'cache/*') as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    /**
     * Test file loader
     */
    public function testFileLoader()
    {
        $cacheDir = __DIR__.DIRECTORY_SEPARATOR.'cache';
        $dataDir = __DIR__.DIRECTORY_SEPARATOR.'data';
        $loader = new FileLoader($cacheDir);
        $loader->addDirectory($dataDir);

        $this->assertFalse(file_exists($cacheDir.DIRECTORY_SEPARATOR.'test.yml.php'));

        $values = $loader->load('test.yml');

        $this->assertEquals('bar', $values['foo']);

        $this->assertTrue(file_exists($cacheDir.DIRECTORY_SEPARATOR.'test.yml.php'));
    }
}
