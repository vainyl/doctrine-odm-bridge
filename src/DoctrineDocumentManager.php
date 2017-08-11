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

use Doctrine\Common\EventManager;
use Doctrine\Common\Persistence\Mapping\MappingException;
use Doctrine\MongoDB\Connection;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\DocumentManager;
use Vainyl\Doctrine\ODM\Exception\LevelIntegrityException;
use Vainyl\Domain\DomainInterface;
use Vainyl\Domain\Metadata\Factory\DomainMetadataFactoryInterface;
use Vainyl\Domain\Scenario\Storage\DomainScenarioStorageInterface;
use Vainyl\Domain\Storage\DomainStorageInterface;
use Vainyl\Time\Factory\TimeFactoryInterface;

/**
 * Class DoctrineDocumentManager
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class DoctrineDocumentManager extends DocumentManager implements DomainStorageInterface, DomainScenarioStorageInterface
{
    private $timeFactory;

    private $domainMetadataFactory;

    private $flushLevel = 0;

    /**
     * DoctrineDocumentManager constructor.
     *
     * @param Connection                     $conn
     * @param Configuration                  $config
     * @param EventManager                   $eventManager
     * @param TimeFactoryInterface           $timeFactory
     * @param DomainMetadataFactoryInterface $domainMetadataFactory
     */
    protected function __construct(
        Connection $conn,
        Configuration $config,
        EventManager $eventManager,
        TimeFactoryInterface $timeFactory,
        DomainMetadataFactoryInterface $domainMetadataFactory
    ) {
        $this->timeFactory = $timeFactory;
        $this->domainMetadataFactory = $domainMetadataFactory;
        parent::__construct($conn, $config, $eventManager);
    }

    /**
     * @param mixed                          $conn
     * @param Configuration                  $config
     * @param EventManager                   $eventManager
     * @param TimeFactoryInterface           $timeFactory
     * @param DomainMetadataFactoryInterface $domainMetadataFactory
     *
     * @return DoctrineDocumentManager
     */
    public static function createExtended(
        $conn,
        Configuration $config,
        EventManager $eventManager,
        TimeFactoryInterface $timeFactory,
        DomainMetadataFactoryInterface $domainMetadataFactory
    ) {
        return new DoctrineDocumentManager($conn, $config, $eventManager, $timeFactory, $domainMetadataFactory);
    }

    /**
     * @param DoctrineDocumentManager $obj
     *
     * @return bool
     */
    public function equals($obj): bool
    {
        return $this->getId() === $obj->getId();
    }

    /**
     * @inheritDoc
     */
    public function findById(string $name, $id): ?DomainInterface
    {
        return $this->getRepository($name)->find($id);
    }

    /**
     * @inheritDoc
     */
    public function findMany(
        string $name,
        array $criteria = [],
        array $orderBy = [],
        int $limit = 0,
        int $offset = 0
    ): array {
        return $this->getRepository($name)->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @inheritDoc
     */
    public function findOne(string $name, array $criteria = [], array $orderBy = []): ?DomainInterface
    {
        if ([] === ($result = $this->getRepository($name)->findBy($criteria, $orderBy, 1))) {
            return null;
        }

        return current($result);
    }

    /**
     * @inheritDoc
     */
    public function flush($entity = null, array $options = [])
    {
        $this->flushLevel--;

        if (0 < $this->flushLevel) {
            return $this;
        }

        if (0 > $this->flushLevel) {
            throw new LevelIntegrityException($this, $this->flushLevel);
        }

        parent::flush($entity, $options);

        return $this;
    }

    /**
     * @return DomainMetadataFactoryInterface
     */
    public function getDomainMetadataFactory(): DomainMetadataFactoryInterface
    {
        return $this->domainMetadataFactory;
    }

    /**
     * @inheritDoc
     */
    public function getId(): ?string
    {
        return spl_object_hash($this);
    }

    /**
     * @inheritDoc
     */
    public function getScenarios(string $name): array
    {
        return $this->getMetadataFactory()->getMetadataFor($name)->getDomainMetadata()->getScenarios();
    }

    /**
     * @return TimeFactoryInterface
     */
    public function getTimeFactory()
    {
        return $this->timeFactory;
    }

    /**
     * @inheritDoc
     */
    public function hash()
    {
        return $this->getId();
    }

    /**
     * @inheritDoc
     */
    public function init()
    {
        if (0 <= $this->flushLevel) {
            $this->flushLevel++;

            return $this;
        }

        throw new LevelIntegrityException($this, $this->flushLevel);
    }

    /**
     * @inheritDoc
     */
    public function supports(string $name): bool
    {
        try {
            $this->getMetadataFactory()->getMetadataFor($name);
        } catch (MappingException $e) {
            return false;
        }

        return true;
    }
}
