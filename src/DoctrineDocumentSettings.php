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

namespace Vainyl\Doctrine\ODM;

use Vainyl\Doctrine\Common\DoctrineSettings;

/**
 * Class DoctrineODMSettings
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class DoctrineDocumentSettings extends DoctrineSettings
{
    private $databaseName;

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
     * @param string           $globalFileName
     * @param string           $fileExtension
     * @param string           $tmpDir
     * @param string           $proxyNamespace
     * @param string           $hydratorNamespace
     */
    public function __construct(
        DoctrineSettings $doctrineSettings,
        string $databaseName,
        string $globalFileName,
        string $fileExtension,
        string $tmpDir,
        string $proxyNamespace,
        string $hydratorNamespace
    ) {
        $this->databaseName = $databaseName;
        $this->globalFileName = $globalFileName;
        $this->fileExtension = $fileExtension;
        $this->tmpDir = $tmpDir;
        $this->proxyNamespace = $proxyNamespace;
        $this->hydratorNamespace = $hydratorNamespace;
        parent::__construct(
            $doctrineSettings->getCache(),
            $doctrineSettings->getDriverName(),
            $doctrineSettings->getExtraPaths()
        );
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