<?php
/*
 * Copyright (c) 2015 Matthew Loberg
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

namespace Mlo\FileLoader;

use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Config\Resource\FileResource;

/**
 * FileLoader
 *
 * @author Matthew Loberg <loberg.matt@gmail.com>
 */
class FileLoader
{
    /**
     * @var array
     */
    private $directories;

    /**
     * @var string
     */
    private $cacheDirectory;

    /**
     * @var array
     */
    private $loaders;

    /**
     * Constructor
     *
     * @param string $cacheDirectory
     * @param array  $directories
     * @param array  $loaders
     */
    public function __construct($cacheDirectory, array $directories = [], array $loaders = null)
    {
        $this->cacheDirectory = $cacheDirectory;
        $this->directories    = $directories;
        $this->loaders        = $loaders;

        if (null === $this->loaders) {
            $this->loaders = [
                __NAMESPACE__ . '\\JsonFileLoader',
                __NAMESPACE__ . '\\YamlFileLoader',
            ];
        }
    }

    /**
     * Get directories
     *
     * @return array
     */
    public function getDirectories()
    {
        return $this->directories;
    }

    /**
     * Set directories
     *
     * @param array $directories
     *
     * @return FileLoader
     */
    public function setDirectories(array $directories)
    {
        $this->directories = $directories;

        return $this;
    }

    /**
     * Add directory
     *
     * @param string $directory
     *
     * @return FileLoader
     */
    public function addDirectory($directory)
    {
        if (!in_array($directory, $this->directories)) {
            $this->directories[] = $directory;
        }

        return $this;
    }

    /**
     * Get cache directory
     *
     * @return string
     */
    public function getCacheDirectory()
    {
        return $this->cacheDirectory;
    }

    /**
     * Set cache directory
     *
     * @param string $cacheDirectory
     *
     * @return FileLoader
     */
    public function setCacheDirectory($cacheDirectory)
    {
        $this->cacheDirectory = $cacheDirectory;

        return $this;
    }

    /**
     * Add loader
     *
     * @param string $loader
     *
     * @return FileLoader
     */
    public function addLoader($loader)
    {
        $this->loaders[] = $loader;

        return $this;
    }

    /**
     * Load from file
     *
     * @param string $fileName
     * @param bool   $refresh
     *
     * @return array
     * @throws \Symfony\Component\Config\Exception\FileLoaderLoadException
     */
    public function load($fileName, $refresh = false)
    {
        $cachePath = $this->getCacheDirectory() . DIRECTORY_SEPARATOR . $fileName . '.php';

        $cache = new ConfigCache($cachePath, true);

        if ($refresh || !$cache->isFresh()) {
            $locator = new FileLocator($this->getDirectories());

            $loaderResolver = new LoaderResolver(array_map(function ($loader) use ($locator) {
                return new $loader($locator);
            }, $this->loaders));

            $filePath = $locator->locate($fileName);
            $resource = new FileResource($filePath);

            $delegatingLoader = new DelegatingLoader($loaderResolver);
            $values = $delegatingLoader->load($filePath);

            $retval = sprintf("<?php\nreturn %s;", var_export($values, true));

            $cache->write($retval, [$resource]);
        } else {
            $values = require($cachePath);
        }

        return $values;
    }

    /**
     * Load from file and apply configuration
     *
     * @param string                 $fileName
     * @param ConfigurationInterface $configuration
     * @param bool                   $refresh
     *
     * @return array
     */
    public function loadWithConfiguration($fileName, ConfigurationInterface $configuration, $refresh = false)
    {
        $tree = $configuration->getConfigTreeBuilder()->buildTree();
        $name = $tree->getName();

        $cachePath = $this->getCacheDirectory() . DIRECTORY_SEPARATOR . $fileName . '.config.' . $name . '.php';

        $cache = new ConfigCache($cachePath, true);

        if ($refresh || !$cache->isFresh()) {
            $config = $this->load($fileName, $refresh);

            $refClass = new \ReflectionClass(get_class($configuration));
            $resource = new FileResource($refClass->getFileName());

            $processor = new Processor();
            $values = $processor->process($tree, $config);

            $retval = sprintf("<?php\nreturn %s;", var_export($values, true));

            $cache->write($retval, [$resource]);
        } else {
            $values = require($cachePath);
        }

        return $values;
    }
}
