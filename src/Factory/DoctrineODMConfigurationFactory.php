<?php
/**
 * Vainyl
 *
 * PHP Version 7
 *
 * @package   Doctrine-ODM-Bridge
 * @license   https://opensource.org/licenses/MIT MIT License
 * @link      https://vainyl.com
 */
declare(strict_types=1);

namespace Vainyl\Doctrine\ODM\Factory;

use Doctrine\Common\Persistence\Mapping\Driver\FileDriver;
use Doctrine\ODM\MongoDB\Configuration;
use Vainyl\Core\AbstractIdentifiable;
use Vainyl\Core\Application\EnvironmentInterface;
use Vainyl\Doctrine\ODM\DoctrineDocumentSettings;

/**
 * Class DoctrineODMConfigurationFactory
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class DoctrineODMConfigurationFactory extends AbstractIdentifiable
{
    /**
     * @param EnvironmentInterface     $environment
     * @param DoctrineDocumentSettings $settings
     * @param FileDriver               $driver
     *
     * @return Configuration
     */
    public function getConfiguration(
        EnvironmentInterface $environment,
        DoctrineDocumentSettings $settings,
        FileDriver $driver
    ): Configuration {
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
