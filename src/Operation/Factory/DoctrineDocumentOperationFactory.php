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

namespace Vainyl\Doctrine\ODM\Operation\Factory;

use Doctrine\Common\Persistence\ObjectManager as DocumentManagerInterface;
use Doctrine\ODM\MongoDB\Mapping\MappingException;
use Vainyl\Core\AbstractIdentifiable;
use Vainyl\Doctrine\ODM\Operation\CreateDoctrineDocumentOperation;
use Vainyl\Doctrine\ODM\Operation\DeleteDoctrineDocumentOperation;
use Vainyl\Doctrine\ODM\Operation\UpdateDoctrineDocumentOperation;
use Vainyl\Doctrine\ODM\Operation\UpsertDoctrineDocumentOperation;
use Vainyl\Document\DocumentInterface;
use Vainyl\Document\Operation\Factory\DocumentOperationFactoryInterface;
use Vainyl\Domain\DomainInterface;
use Vainyl\Operation\Collection\Factory\CollectionFactoryInterface;
use Vainyl\Operation\OperationInterface;

/**
 * Class DoctrineDocumentOperationFactory
 *
 * @author Nazar Ivanenko <nivanenko@gmail.com>
 */
class DoctrineDocumentOperationFactory extends AbstractIdentifiable implements
    DocumentOperationFactoryInterface
{
    private $collectionFactory;

    private $documentManager;

    /**
     * DoctrineDocumentOperationFactory constructor.
     *
     * @param CollectionFactoryInterface $collectionFactory
     * @param DocumentManagerInterface   $documentManager
     */
    public function __construct(
        CollectionFactoryInterface $collectionFactory,
        DocumentManagerInterface $documentManager
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->documentManager = $documentManager;
    }

    /**
     * @param DocumentInterface $domain
     *
     * @return OperationInterface
     */
    public function create(DomainInterface $domain): OperationInterface
    {
        return new CreateDoctrineDocumentOperation($this->documentManager, $domain);
    }

    /**
     * @param DocumentInterface $domain
     *
     * @return OperationInterface
     */
    public function delete(DomainInterface $domain): OperationInterface
    {
        return new DeleteDoctrineDocumentOperation($this->documentManager, $domain);
    }

    /**
     * @inheritDoc
     */
    public function supports(DomainInterface $domain): bool
    {
        try {
            $this->documentManager->getMetadataFactory()->getMetadataFor(get_class($domain));
        } catch (MappingException $e) {
            return false;
        }

        return true;
    }

    /**
     * @param DocumentInterface $newDomain
     * @param DocumentInterface $oldDomain
     *
     * @return OperationInterface
     */
    public function update(DomainInterface $newDomain, DomainInterface $oldDomain): OperationInterface
    {
        return new UpdateDoctrineDocumentOperation($this->documentManager, $newDomain, $oldDomain);
    }

    /**
     * @param DocumentInterface $domain
     *
     * @return OperationInterface
     */
    public function upsert(DomainInterface $domain): OperationInterface
    {
        return new UpsertDoctrineDocumentOperation($this->documentManager, $domain);
    }
}
