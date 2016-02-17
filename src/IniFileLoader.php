<?php
/*
 * Copyright (c) 2016 Matthew Loberg
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

namespace Mlo\FileLoader;

use Symfony\Component\Config\Loader\Loader;

/**
 * IniFileLoader
 *
 * @author Matthew Loberg <loberg.matt@gmail.com>
 */
class IniFileLoader extends Loader
{
    /**
     * @inheritDoc
     */
    public function load($resource, $type = null)
    {
        return parse_ini_file($resource);
    }

    /**
     * @inheritDoc
     */
    public function supports($resource, $type = null)
    {
        return is_string($resource) && 'ini' === pathinfo($resource, PATHINFO_EXTENSION);
    }
}
