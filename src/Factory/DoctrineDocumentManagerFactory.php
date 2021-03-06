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

use Doctrine\Common\EventManager as DoctrineEventManager;
use Doctrine\MongoDB\Connection;
use Doctrine\ODM\MongoDB\Configuration;
use Vainyl\Doctrine\ODM\DoctrineDocumentManager;
use Vainyl\Domain\Metadata\Factory\DomainMetadataFactoryInterface;
use Vainyl\Time\Factory\TimeFactoryInterface;

/**
 * Class DoctrineDocumentManagerFactory
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class DoctrineDocumentManagerFactory
{
    /**
     * @param Connection                     $connection
     * @param Configuration                  $configuration
     * @param DoctrineEventManager           $eventManager
     * @param TimeFactoryInterface           $timeFactory
     * @param DomainMetadataFactoryInterface $domainMetadataFactory
     *
     * @return DoctrineDocumentManager
     */
    public function create(
        Connection $connection,
        Configuration $configuration,
        DoctrineEventManager $eventManager,
        TimeFactoryInterface $timeFactory,
        DomainMetadataFactoryInterface $domainMetadataFactory
    ): DoctrineDocumentManager {
        return DoctrineDocumentManager::createExtended(
            $connection,
            $configuration,
            $eventManager,
            $timeFactory,
            $domainMetadataFactory
        );
    }
}
