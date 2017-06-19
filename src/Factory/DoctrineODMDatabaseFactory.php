<?php
/**
 * Vainyl
 *
 * PHP Version 7
 *
 * @package   Doctrine-common-bridge
 * @license   https://opensource.org/licenses/MIT MIT License
 * @link      https://vainyl.com
 */
declare(strict_types=1);

namespace Vainyl\Doctrine\ODM\Factory;

use Doctrine\Common\EventManager;
use Vainyl\Core\AbstractIdentifiable;
use Vainyl\Core\Storage\StorageInterface;
use Vainyl\Database\DatabaseInterface;
use Vainyl\Database\Factory\DatabaseFactoryInterface;
use Vainyl\Doctrine\ODM\Database\DoctrineMongoDatabase;

/**
 * Class DoctrineODMDatabaseFactory
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class DoctrineODMDatabaseFactory extends AbstractIdentifiable implements DatabaseFactoryInterface
{
    private $connectionStorage;

    private $eventManager;

    /**
     * DoctrineODMDatabaseFactory constructor.
     *
     * @param StorageInterface $connectionStorage
     */
    public function __construct(
        StorageInterface $connectionStorage,
        EventManager $eventManager
    ) {
        $this->connectionStorage = $connectionStorage;
        $this->eventManager = $eventManager;
    }

    /**
     * @inheritDoc
     */
    public function createDatabase(string $name, string $connectionName, array $options = []): DatabaseInterface
    {
        return new DoctrineMongoDatabase(
            $name, $this->connectionStorage[$connectionName], $this->eventManager, $options
        );
    }
}