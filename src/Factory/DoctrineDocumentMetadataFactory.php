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

namespace Vainyl\Doctrine\ODM\Factory;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadataFactory;
use Vainyl\Doctrine\ODM\DoctrineDocumentManager;
use Vainyl\Doctrine\ODM\DoctrineDocumentMetadata;

/**
 * Class DoctrineDocumentMetadataFactory
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 *
 * @method DoctrineDocumentMetadata getMetadataFor($className)
 */
class DoctrineDocumentMetadataFactory extends ClassMetadataFactory
{
    /**
     * @var DoctrineDocumentManager
     */
    private $documentManager;

    /**
     * @inheritDoc
     */
    protected function newClassMetadataInstance($className)
    {
        return new DoctrineDocumentMetadata($className, $this->documentManager->getDomainMetadataFactory()->create());
    }

    /**
     * @param DoctrineDocumentManager $dm
     */
    public function setDocumentManager(DocumentManager $dm)
    {
        $this->documentManager = $dm;
        parent::setDocumentManager($dm);
    }
}