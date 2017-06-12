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

use Doctrine\Common\EventManager as DoctrineEventManager;
use Doctrine\MongoDB\Connection;
use Doctrine\ODM\MongoDB\Configuration;
use Vainyl\Doctrine\ODM\DoctrineDocumentManager;
use Vainyl\Time\Factory\TimeFactoryInterface;

/**
 * Class DoctrineDocumentManagerFactory
 *
 * @author Nazar Ivanenko <nivanenko@gmail.com>
 */
class DoctrineDocumentManagerFactory
{
    /**
     * @param array $config
     *
     * @return string
     */
    protected function getPassword(array $config): string
    {
        if (false === array_key_exists('password', $config)) {
            return '';
        }

        $password = $config['password'];

        if (false === array_key_exists('algo', $config)) {
            return $password;
        }

        return hash($config['algo'], $password);
    }

    /**
     * @param array $config
     *
     * @return array
     */
    protected function getCredentials(array $config): array
    {
        $hostsConfig = $config['hosts'];
        $connectionStrings = [];
        foreach ($hostsConfig as $hostConfig) {
            $port = 27017;
            if (false !== array_key_exists('port', $hostConfig)) {
                $port = $hostConfig['port'];
            }
            $connectionStrings[] = sprintf('%s:%d', $hostConfig['host'], $port);
        }
        $connectionString = implode(',', $connectionStrings);

        $options = [];
        if (false !== array_key_exists('options', $config)) {
            $options = $config['options'];
        }

        $driverOptions = [];
        if (false !== array_key_exists('driverOptions', $config)) {
            $options = $config['driverOptions'];
        }

        return [
            $config['username'],
            $config['password'],
            $connectionString,
            $config['dbname'],
            $options,
            $driverOptions,
        ];
    }

    /**
     * @param \ArrayAccess              $configData
     * @param string                    $connectionName
     * @param Configuration             $configuration
     * @param DoctrineEventManager      $eventManager
     * @param TimeFactoryInterface      $timeFactory
     *
     * @return DoctrineDocumentManager
     */
    public function create(
        \ArrayAccess $configData,
        $connectionName,
        Configuration $configuration,
        DoctrineEventManager $eventManager,
        TimeFactoryInterface $timeFactory
    ) : DoctrineDocumentManager
    {
        list ($username, $password, $connectionString, $database, $options, $driverOptions)
            = $this->getCredentials($configData['connections'][$connectionName]);
        $dsn = sprintf('mongodb://%s:%s@%s/', $username, $password, $connectionString);
        $configuration->setDefaultDB($configData['connections'][$connectionName]['dbname']);
        return DoctrineDocumentManager::createWithTimeFactory(
            new Connection($dsn, $options, $configuration, $eventManager, $driverOptions),
            $configuration,
            $eventManager,
            $timeFactory
        );
    }
}
