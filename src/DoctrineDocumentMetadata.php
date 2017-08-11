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

namespace Vainyl\Doctrine\ODM;

use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Vainyl\Doctrine\Common\Metadata\DoctrineDomainMetadataInterface;
use Vainyl\Domain\Metadata\DomainMetadataInterface;

/**
 * Class DoctrineDocumentMetadata
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class DoctrineDocumentMetadata extends ClassMetadata implements DoctrineDomainMetadataInterface
{
    public $domainMetadata;

    /**
     * DoctrineEntityMetadata constructor.
     *
     * @param string                  $documentName
     * @param DomainMetadataInterface $domainMetadata
     */
    public function __construct($documentName, DomainMetadataInterface $domainMetadata)
    {
        $this->domainMetadata = $domainMetadata;
        parent::__construct($documentName);
    }

    /**
     * @inheritDoc
     */
    public function __sleep()
    {
        return array_merge(parent::__sleep(), ['domainMetadata']);
    }

    /**
     * @inheritDoc
     */
    public function getDomainMetadata(): DomainMetadataInterface
    {
        return $this->domainMetadata;
    }
}