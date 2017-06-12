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

use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\Common\Cache\Cache as DoctrineCacheInterface;
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
     * @param EnvironmentInterface                 $environment
     * @param string                 $configDir
     * @param string                 $cacheDir
     * @param string                 $globalFileName
     * @param string                 $extension
     *
     * @return Configuration
     */
    public function getConfiguration(
        DoctrineCacheInterface $doctrineCache,
        EnvironmentInterface $environment,
        string $configDir,
        string $cacheDir,
        string $globalFileName,
        string $extension
    ) : Configuration
    {
        $paths = [];
        /**
         * @var AbstractExtension $extension
         */
        foreach ($this->extensionStorage->getIterator() as $extension) {
            $paths[$extension->getConfigDirectory($environment)] = $extension->getNamespace();
        }
        $paths[$configDir] = '';

        $driver = new SimplifiedYamlDriver($paths, $extension);
        $driver->setGlobalBasename($globalFileName);

        $config = new Configuration();
        $config->setProxyDir($cacheDir.'/proxies');
        $config->setProxyNamespace('Proxies');
        $config->setHydratorDir($cacheDir.'/hydrators');
        $config->setHydratorNamespace('Hydrators');
        $config->setMetadataDriverImpl($driver);
        $config->setMetadataCacheImpl($doctrineCache);
        $config->setAutoGenerateProxyClasses($environment->isDebugEnabled());

        return $config;
    }
}
