<?php
/**
 * Vainyl
 *
 * PHP Version 7
 *
 * @package   Doctrine
 * @license   https://opensource.org/licenses/MIT MIT License
 * @link      https://vainyl.com
 */
declare(strict_types=1);

namespace Vainyl\Doctrine\ODM\Factory;

use Doctrine\Common\Cache\Cache as DoctrineCacheInterface;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\Mapping\Driver\SimplifiedYamlDriver;
use Vainyl\Core\Application\EnvironmentInterface;
use Vainyl\Core\Extension\AbstractExtension;
use Vainyl\Core\Storage\StorageInterface;

/**
 * Class DoctrineConfigurationFactory
 *
 * @author Nazar Ivanenko <nivanenko@gmail.com>
 */
class DoctrineConfigurationFactory
{
    private $extensionStorage;

    /**
     * DoctrineConfigurationFactory constructor.
     *
     * @param StorageInterface $extensionStorage
     */
    public function __construct(StorageInterface $extensionStorage)
    {
        $this->extensionStorage = $extensionStorage;
    }

    /**
     * @param DoctrineCacheInterface $doctrineCache
     * @param EnvironmentInterface   $environment
     * @param string                 $globalFileName
     * @param string                 $fileExtension
     *
     * @return Configuration
     */
    public function getConfiguration(
        DoctrineCacheInterface $doctrineCache,
        EnvironmentInterface $environment,
        string $globalFileName,
        string $fileExtension
    ): Configuration {
        $paths = [];
        /**
         * @var AbstractExtension $extension
         */
        foreach ($this->extensionStorage->getIterator() as $extension) {
            $paths[$extension->getConfigDirectory($environment)] = $extension->getNamespace();
        }
        $paths[$environment->getConfigDirectory()] = '';

        $driver = new SimplifiedYamlDriver($paths, $fileExtension);
        $driver->setGlobalBasename($globalFileName);

        $config = new Configuration();
        $config->setProxyDir($environment->getCacheDirectory() . DIRECTORY_SEPARATOR . 'proxies');
        $config->setProxyNamespace('Proxies');
        $config->setHydratorDir($environment->getCacheDirectory() . DIRECTORY_SEPARATOR . 'proxies');
        $config->setHydratorNamespace('Hydrators');
        $config->setMetadataDriverImpl($driver);
        $config->setMetadataCacheImpl($doctrineCache);
        $config->setAutoGenerateProxyClasses($environment->isDebugEnabled());

        return $config;
    }
}
