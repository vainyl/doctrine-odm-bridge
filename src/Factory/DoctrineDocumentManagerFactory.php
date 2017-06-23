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
     * @param Connection           $connection
     * @param Configuration        $configuration
     * @param DoctrineEventManager $eventManager
     * @param TimeFactoryInterface $timeFactory
     *
     * @return DoctrineDocumentManager
     */
    public function create(
        Connection $connection,
        Configuration $configuration,
        DoctrineEventManager $eventManager,
        TimeFactoryInterface $timeFactory
    ): DoctrineDocumentManager {
        return DoctrineDocumentManager::createWithTimeFactory(
            $connection,
            $configuration,
            $eventManager,
            $timeFactory
        );
    }
}
