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

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Cache\Cache as DoctrineCacheInterface;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Doctrine\ODM\MongoDB\Mapping\Driver\SimplifiedYamlDriver;
use Doctrine\ODM\MongoDB\Mapping\Driver\XmlDriver;
use Vainyl\Core\AbstractIdentifiable;
use Vainyl\Core\Application\EnvironmentInterface;
use Vainyl\Core\Extension\AbstractExtension;
use Vainyl\Doctrine\ODM\Exception\UnknownDoctrineConfigTypeException;

/**
 * Class DoctrineODMConfigurationFactory
 *
 * @author Nazar Ivanenko <nivanenko@gmail.com>
 */
class DoctrineODMConfigurationFactory extends AbstractIdentifiable
{
    private $bundleStorage;

    /**
     * DoctrineConfigurationFactory constructor.
     *
     * @param \Traversable $bundleStorage
     */
    public function __construct(\Traversable $bundleStorage)
    {
        $this->bundleStorage = $bundleStorage;
    }

    /**
     * @param DoctrineCacheInterface $doctrineCache
     * @param EnvironmentInterface   $environment
     * @param string                 $databaseName
     * @param string                 $driverName
     * @param string                 $globalFileName
     * @param string                 $fileExtension
     * @param string                 $tmpDir
     * @param string                 $proxyNamespace
     * @param string                 $hydratorNamespace
     *
     * @return Configuration
     */
    public function getConfiguration(
        DoctrineCacheInterface $doctrineCache,
        EnvironmentInterface $environment,
        string $databaseName,
        string $driverName,
        string $globalFileName,
        string $fileExtension,
        string $tmpDir,
        string $proxyNamespace,
        string $hydratorNamespace
    ): Configuration {
        $paths = [];
        /**
         * @var AbstractExtension $bundle
         */
        foreach ($this->bundleStorage as $bundle) {
            $paths[$bundle->getConfigDirectory()] = $bundle->getNamespace();
        }
        $paths[$environment->getConfigDirectory()] = '';

        switch ($driverName) {
            case 'yaml':
                $driver = new SimplifiedYamlDriver($paths, $fileExtension);
                break;
            case 'xml':
                $driver = new XmlDriver($paths, $fileExtension);
                break;
            case 'annotation':
                $driver = new AnnotationDriver(new AnnotationReader(), $paths);
                break;
            default:
                throw new UnknownDoctrineConfigTypeException($this, $driverName);
        }

        $driver->setGlobalBasename($globalFileName);
        $config = new Configuration();
        $config->setDefaultDB($databaseName);
        $config->setProxyDir($environment->getCacheDirectory() . DIRECTORY_SEPARATOR . $tmpDir);
        $config->setProxyNamespace($proxyNamespace);
        $config->setHydratorDir($environment->getCacheDirectory() . DIRECTORY_SEPARATOR . $tmpDir);
        $config->setHydratorNamespace($hydratorNamespace);
        $config->setMetadataDriverImpl($driver);
        $config->setMetadataCacheImpl($doctrineCache);
        $config->setAutoGenerateProxyClasses($environment->isDebugEnabled());

        return $config;
    }
}
