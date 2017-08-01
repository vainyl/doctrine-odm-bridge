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
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Doctrine\ODM\MongoDB\Mapping\Driver\SimplifiedYamlDriver;
use Doctrine\ODM\MongoDB\Mapping\Driver\XmlDriver;
use Vainyl\Core\AbstractIdentifiable;
use Vainyl\Core\Application\EnvironmentInterface;
use Vainyl\Core\Extension\ExtensionInterface;
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
     * @param EnvironmentInterface $environment
     * @param DoctrineODMSettings  $settings
     *
     * @return Configuration
     *
     * @throws UnknownDoctrineConfigTypeException
     */
    public function getConfiguration(EnvironmentInterface $environment, DoctrineODMSettings $settings): Configuration
    {
        $paths = [];
        foreach ($settings->getExtraPaths() as $extraPath) {
            $paths[$extraPath['dir']] = $extraPath['prefix'];
        }
        /**
         * @var ExtensionInterface $bundle
         */
        foreach ($this->bundleStorage as $bundle) {
            $paths[$bundle->getConfigDirectory()] = $bundle->getNamespace();
        }
        $paths[$environment->getConfigDirectory()] = '';

        switch ($settings->getDriverName()) {
            case 'yaml':
                $driver = new SimplifiedYamlDriver($paths, $settings->getFileExtension());
                break;
            case 'xml':
                $driver = new XmlDriver($paths, $settings->getFileExtension());
                break;
            case 'annotation':
                $driver = new AnnotationDriver(new AnnotationReader(), $paths);
                break;
            default:
                throw new UnknownDoctrineConfigTypeException($this, $settings->getDriverName());
        }

        $driver->setGlobalBasename($settings->getGlobalFileName());
        $config = new Configuration();
        $config->setDefaultDB($settings->getDatabaseName());
        $config->setProxyDir($environment->getCacheDirectory() . DIRECTORY_SEPARATOR . $settings->getTmpDir());
        $config->setProxyNamespace($settings->getProxyNamespace());
        $config->setHydratorDir($environment->getCacheDirectory() . DIRECTORY_SEPARATOR . $settings->getTmpDir());
        $config->setHydratorNamespace($settings->getHydratorNamespace());
        $config->setMetadataDriverImpl($driver);
        $config->setMetadataCacheImpl($settings->getCache());
        $config->setAutoGenerateProxyClasses($environment->isDebugEnabled());

        return $config;
    }
}
