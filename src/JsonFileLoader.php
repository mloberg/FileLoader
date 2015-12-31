<?php
/*
 * Copyright (c) 2015 Matthew Loberg
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

namespace Mlo\FileLoader;

use Symfony\Component\Config\Loader\FileLoader as BaseFileLoader;

/**
 * JsonFileLoader
 *
 * @author Matthew Loberg <loberg.matt@gmail.com>
 */
class JsonFileLoader extends BaseFileLoader
{
    /**
     * @inheritDoc
     */
    public function load($resource, $type = null)
    {
        return json_decode(file_get_contents($resource), true);
    }

    /**
     * @inheritDoc
     */
    public function supports($resource, $type = null)
    {
        return is_string($resource) && 'json' === pathinfo($resource, PATHINFO_EXTENSION);
    }
}
