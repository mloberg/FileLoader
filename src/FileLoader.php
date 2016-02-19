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
use Symfony\Component\Config\Loader\LoaderInterface;
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
     * @var string
     */
    private $cacheDirectory;

    /**
     * @var array
     */
    private $directories;

    /**
     * @var LoaderInterface[]|array
     */
    private $loaders = [];

    /**
     * @var bool
     */
    private $debug;

    /**
     * Constructor
     *
     * @param string                  $cacheDirectory
     * @param array|string            $directories
     * @param LoaderInterface[]|array $loaders
     * @param bool                    $debug
     */
    public function __construct($cacheDirectory, $directories = null, array $loaders = [], $debug = false)
    {
        $this->cacheDirectory = $cacheDirectory;
        $this->directories    = (array) $directories;
        $this->debug          = $debug;

        foreach ($loaders as $loader) {
            $this->addLoader($loader);
        }
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
     * Get Debug
     *
     * @return bool
     */
    public function isDebug()
    {
        return $this->debug;
    }

    /**
     * Set Debug
     *
     * @param bool $debug
     *
     * @return FileLoader
     */
    public function setDebug($debug)
    {
        $this->debug = $debug;

        return $this;
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
     * Get Loaders
     *
     * @return LoaderInterface[]|array
     */
    public function getLoaders()
    {
        return $this->loaders;
    }

    /**
     * Add loader
     *
     * @param LoaderInterface $loader
     *
     * @return FileLoader
     */
    public function addLoader(LoaderInterface $loader)
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
        $cache     = new ConfigCache($cachePath, $this->debug);

        if ($refresh || !$cache->isFresh()) {
            $resolver = new LoaderResolver($this->loaders);
            $loader   = new DelegatingLoader($resolver);
            $locator  = new FileLocator($this->getDirectories());

            $filePath = $locator->locate($fileName);
            $values   = $loader->load($filePath);
            $resource = new FileResource($filePath);

            $cacheValue = sprintf("<?php return %s;", var_export($values, true));

            $cache->write($cacheValue, [$resource]);
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
        $cache     = new ConfigCache($cachePath, $this->debug);

        if ($refresh || !$cache->isFresh()) {
            $reflection = new \ReflectionClass(get_class($configuration));
            $resource   = new FileResource($reflection->getFileName());

            $processor = new Processor();
            $config    = $this->load($fileName, $refresh);
            $values    = $processor->process($tree, $config);

            $cacheValue = sprintf("<?php return %s;", var_export($values, true));

            $cache->write($cacheValue, [$resource]);
        } else {
            $values = require($cachePath);
        }

        return $values;
    }
}
