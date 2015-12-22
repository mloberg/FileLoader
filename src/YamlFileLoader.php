<?php
/**
 * YamlFileLoader.php
 *
 * @package Mlo\FileLoader
 */

namespace Mlo\FileLoader;

use Symfony\Component\Config\Loader\FileLoader as SymfonyFileLoader;
use Symfony\Component\Yaml\Yaml;

/**
 * YamlFileLoader
 *
 * @author Matthew Loberg <loberg.matt@gmail.com>
 */
class YamlFileLoader extends SymfonyFileLoader
{
    /**
     * {@inheritdoc}
     */
    public function load($resource, $type = null)
    {
        $values = Yaml::parse(file_get_contents($resource));

        return $values;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        return is_string($resource) && 'yml' === pathinfo($resource, PATHINFO_EXTENSION);
    }
}
