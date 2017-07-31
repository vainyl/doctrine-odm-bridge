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
use Vainyl\Doctrine\ODM\Operation\CreateDoctrineDocumentOperation;
use Vainyl\Doctrine\ODM\Operation\DeleteDoctrineDocumentOperation;
use Vainyl\Doctrine\ODM\Operation\UpdateDoctrineDocumentOperation;
use Vainyl\Doctrine\ODM\Operation\UpsertDoctrineDocumentOperation;
use Vainyl\Document\DocumentInterface;
use Vainyl\Document\Operation\Factory\DocumentOperationFactoryInterface;
use Vainyl\Domain\DomainInterface;
use Vainyl\Domain\Operation\Decorator\AbstractDomainOperationFactoryDecorator;
use Vainyl\Domain\Operation\Factory\DomainOperationFactoryInterface;
use Vainyl\Operation\Collection\Factory\CollectionFactoryInterface;
use Vainyl\Operation\OperationInterface;

/**
 * Class DoctrineDocumentOperationFactory
 *
 * @author Nazar Ivanenko <nivanenko@gmail.com>
 */
class DoctrineDocumentOperationFactory extends AbstractDomainOperationFactoryDecorator implements
    DocumentOperationFactoryInterface
{
    private $collectionFactory;

    private $documentManager;

    /**
     * DoctrineDocumentOperationFactory constructor.
     *
     * @param DomainOperationFactoryInterface $operationFactory
     * @param CollectionFactoryInterface      $collectionFactory
     * @param DocumentManagerInterface        $documentManager
     */
    public function __construct(
        DomainOperationFactoryInterface $operationFactory,
        CollectionFactoryInterface $collectionFactory,
        DocumentManagerInterface $documentManager
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->documentManager = $documentManager;
        parent::__construct($operationFactory);
    }

    /**
     * @param DocumentInterface $domain
     *
     * @return OperationInterface
     */
    public function create(DomainInterface $domain): OperationInterface
    {
        return $this->collectionFactory
            ->create()
            ->add(parent::create($domain))
            ->add(new CreateDoctrineDocumentOperation($this->documentManager, $domain));
    }

    /**
     * @param DocumentInterface $domain
     *
     * @return OperationInterface
     */
    public function delete(DomainInterface $domain): OperationInterface
    {
        return $this->collectionFactory
            ->create()
            ->add(parent::delete($domain))
            ->add(new DeleteDoctrineDocumentOperation($this->documentManager, $domain));
    }

    /**
     * @inheritDoc
     */
    public function supports(DomainInterface $domain): bool
    {
        return $this->documentManager->getMetadataFactory()->hasMetadataFor(get_class($domain));
    }

    /**
     * @param DocumentInterface $newDomain
     * @param DocumentInterface $oldDomain
     *
     * @return OperationInterface
     */
    public function update(DomainInterface $newDomain, DomainInterface $oldDomain): OperationInterface
    {
        return $this->collectionFactory
            ->create()
            ->add(parent::update($newDomain, $oldDomain))
            ->add(new UpdateDoctrineDocumentOperation($this->documentManager, $newDomain, $oldDomain));
    }

    /**
     * @param DocumentInterface $domain
     *
     * @return OperationInterface
     */
    public function upsert(DomainInterface $domain): OperationInterface
    {
        return $this->collectionFactory
            ->create()
            ->add(parent::upsert($domain))
            ->add(new UpsertDoctrineDocumentOperation($this->documentManager, $domain));
    }
}
