<?php
/*
 * Copyright (c) 2015 Matthew Loberg
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

namespace Mlo\FileLoader;

use Symfony\Component\Config\Loader\FileLoader as BaseFileLoader;
use Symfony\Component\Yaml\Yaml;

/**
 * YamlFileLoader
 *
 * @author Matthew Loberg <loberg.matt@gmail.com>
 */
class YamlFileLoader extends BaseFileLoader
{
    /**
     * @inheritdoc
     */
    public function load($resource, $type = null)
    {
        $values = Yaml::parse(file_get_contents($resource));

        return $values;
    }

    /**
     * @inheritdoc
     */
    public function supports($resource, $type = null)
    {
        return is_string($resource) && 'yml' === pathinfo($resource, PATHINFO_EXTENSION);
    }
}
