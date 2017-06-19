<?php
/**
 * Vainyl
 *
 * PHP Version 7
 *
 * @package   Doctrine-odm-bridge
 * @license   https://opensource.org/licenses/MIT MIT License
 * @link      https://vainyl.com
 */
declare(strict_types=1);

namespace Vainyl\Doctrine\ODM\Database;

use Doctrine\Common\EventManager;
use Doctrine\MongoDB\Connection;
use Vainyl\Connection\ConnectionInterface;
use Vainyl\Database\CursorInterface;
use Vainyl\Database\DatabaseInterface;

/**
 * Class DoctrineMongoDatabase
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class DoctrineMongoDatabase extends Connection implements DatabaseInterface
{
    private $name;

    private $connection;

    /**
     * DoctrineMongoDatabase constructor.
     *
     * @param string              $name
     * @param ConnectionInterface $connection
     * @param EventManager        $eventManager
     * @param array               $options
     */
    public function __construct(
        string $name,
        ConnectionInterface $connection,
        EventManager $eventManager,
        array $options = []
    ) {
        $this->name = $name;
        $this->connection = $connection;
        parent::__construct($connection->establish(), $options, null, $eventManager);
    }

    /**
     * @inheritDoc
     */
    public function runQuery($query, array $bindParams, array $bindTypes = []): CursorInterface
    {
        trigger_error('Method runQuery is not implemented', E_USER_ERROR);

        return null;
    }

    /**
     * @inheritDoc
     */
    public function getId(): string
    {
        return spl_object_hash($this);
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->name;
    }
}