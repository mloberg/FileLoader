<?php
/**
 * FileLoader.php
 *
 * @package Mlo\FileLoader
 */

namespace Mlo\FileLoader;

use Symfony\Component\Config\ConfigCache;
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
     * Constructor
     *
     * @param string $cacheDirectory
     * @param array  $directories
     */
    public function __construct($cacheDirectory, array $directories = [])
    {
        $this->cacheDirectory = $cacheDirectory;
        $this->directories    = $directories;
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
     */
    public function setDirectories(array $directories)
    {
        $this->directories = $directories;
    }

    /**
     * Add directory
     *
     * @param string $directory
     */
    public function addDirectory($directory)
    {
        if (!in_array($directory, $this->directories)) {
            $this->directories[] = $directory;
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
     */
    public function setCacheDirectory($cacheDirectory)
    {
        $this->cacheDirectory = $cacheDirectory;
    }

    /**
     * Load from file
     *
     * @param string $fileName
     * @param bool   $refresh
     * @return mixed
     * @throws \Symfony\Component\Config\Exception\FileLoaderLoadException
     */
    public function load($fileName, $refresh = false)
    {
        $cachePath = $this->getCacheDirectory() . DIRECTORY_SEPARATOR . $fileName . '.php';

        $cache = new ConfigCache($cachePath, true);

        if ($refresh || !$cache->isFresh()) {
            $locator = new FileLocator($this->getDirectories());

            $loaderResolver = new LoaderResolver(array(
                new YamlFileLoader($locator),
            ));

            $delegatingLoader = new DelegatingLoader($loaderResolver);

            $filePath = $locator->locate($fileName);
            $resource = new FileResource($filePath);

            $values = $delegatingLoader->load($filePath);

            $retval = sprintf("<?php\nreturn %s;", var_export($values, true));

            $cache->write($retval, [$resource]);
        } else {
            $values = require((string) $cache);
        }

        return $values;
    }
}
