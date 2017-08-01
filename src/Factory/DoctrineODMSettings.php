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

use Vainyl\Core\AbstractArray;
use Vainyl\Doctrine\Common\DoctrineSettings;
use Doctrine\Common\Cache\Cache as DoctrineCacheInterface;

/**
 * Class DoctrineODMSettings
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class DoctrineODMSettings extends AbstractArray
{
    private $doctrineSettings;

    private $databaseName;

    private $driverName;

    private $globalFileName;

    private $fileExtension;

    private $tmpDir;

    private $proxyNamespace;

    private $hydratorNamespace;

    /**
     * DoctrineODMSettings constructor.
     *
     * @param DoctrineSettings $doctrineSettings
     * @param string           $databaseName
     * @param string           $driverName
     * @param string           $globalFileName
     * @param string           $fileExtension
     * @param string           $tmpDir
     * @param string           $proxyNamespace
     * @param string           $hydratorNamespace
     */
    public function __construct(
        DoctrineSettings $doctrineSettings,
        string $databaseName,
        string $driverName,
        string $globalFileName,
        string $fileExtension,
        string $tmpDir,
        string $proxyNamespace,
        string $hydratorNamespace
    ) {
        $this->doctrineSettings = $doctrineSettings;
        $this->databaseName = $databaseName;
        $this->driverName = $driverName;
        $this->globalFileName = $globalFileName;
        $this->fileExtension = $fileExtension;
        $this->tmpDir = $tmpDir;
        $this->proxyNamespace = $proxyNamespace;
        $this->hydratorNamespace = $hydratorNamespace;
    }

    /**
     * @return DoctrineCacheInterface
     */
    public function getCache(): DoctrineCacheInterface
    {
        return $this->doctrineSettings->getCache();
    }

    /**
     * @return string
     */
    public function getDatabaseName(): string
    {
        return $this->databaseName;
    }

    /**
     * @return string
     */
    public function getDriverName(): string
    {
        return $this->driverName;
    }

    /**
     * @return array
     */
    public function getExtraPaths(): array
    {
        return $this->doctrineSettings->getExtraPaths();
    }

    /**
     * @return string
     */
    public function getFileExtension(): string
    {
        return $this->fileExtension;
    }

    /**
     * @return string
     */
    public function getGlobalFileName(): string
    {
        return $this->globalFileName;
    }

    /**
     * @return string
     */
    public function getHydratorNamespace(): string
    {
        return $this->hydratorNamespace;
    }

    /**
     * @return string
     */
    public function getProxyNamespace(): string
    {
        return $this->proxyNamespace;
    }

    /**
     * @return string
     */
    public function getTmpDir(): string
    {
        return $this->tmpDir;
    }
}