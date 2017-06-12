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

namespace Vainyl\Doctrine\ODM;

use Doctrine\Common\EventManager;
use Doctrine\MongoDB\Connection;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\DocumentManager;
use Vainyl\Doctrine\ODM\Exception\LevelIntegrityException;
use Vainyl\Time\Factory\TimeFactoryInterface;

/**
 * Class DoctrineDocumentManager
 *
 * @author Nazar Ivanenko <nivanenko@gmail.com>
 */
class DoctrineDocumentManager extends DocumentManager
{
    /**
     * @var TimeFactoryInterface
     */
    private $timeFactory;

    private $flushLevel = 0;

    /**
     * DoctrineDocumentManager constructor.
     *
     * @param Connection           $conn
     * @param Configuration        $config
     * @param EventManager         $eventManager
     * @param TimeFactoryInterface $timeFactory
     */
    protected function __construct(
        Connection $conn,
        Configuration $config,
        EventManager $eventManager,
        TimeFactoryInterface $timeFactory
    ) {
        $this->timeFactory = $timeFactory;
        parent::__construct($conn, $config, $eventManager);
    }

    /**
     * @param                      $conn
     * @param Configuration        $config
     * @param EventManager         $eventManager
     * @param TimeFactoryInterface $timeFactory
     *
     * @return DoctrineDocumentManager
     */
    public static function createWithTimeFactory(
        $conn,
        Configuration $config,
        EventManager $eventManager,
        TimeFactoryInterface $timeFactory
    ) {
        return new DoctrineDocumentManager($conn, $config, $eventManager, $timeFactory);
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
    public function flush($entity = null, array $options = null)
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
     * @return TimeFactoryInterface
     */
    public function getTimeFactory()
    {
        return $this->timeFactory;
    }
}
