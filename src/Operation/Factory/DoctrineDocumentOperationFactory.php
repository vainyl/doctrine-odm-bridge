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
use Vainyl\Core\AbstractIdentifiable;
use Vainyl\Doctrine\ODM\Operation\CreateDoctrineDocumentOperation;
use Vainyl\Doctrine\ODM\Operation\DeleteDoctrineDocumentOperation;
use Vainyl\Doctrine\ODM\Operation\UpdateDoctrineDocumentOperation;
use Vainyl\Doctrine\ODM\Operation\UpsertDoctrineDocumentOperation;
use Vainyl\Document\DocumentInterface;
use Vainyl\Document\Operation\Factory\DocumentOperationFactoryInterface;
use Vainyl\Operation\Factory\OperationFactoryInterface;
use Vainyl\Operation\OperationInterface;

/**
 * Class DoctrineDocumentOperationFactory
 *
 * @author Nazar Ivanenko <nivanenko@gmail.com>
 */
class DoctrineDocumentOperationFactory extends AbstractIdentifiable implements DocumentOperationFactoryInterface
{
    private $operationFactory;

    private $documentManager;

    /**
     * DoctrineEntityOperationFactory constructor.
     *
     * @param OperationFactoryInterface $operationFactory
     * @param DocumentManagerInterface  $documentManager
     */
    public function __construct(OperationFactoryInterface $operationFactory, DocumentManagerInterface $documentManager)
    {
        $this->operationFactory = $operationFactory;
        $this->documentManager = $documentManager;
    }

    /**
     * @inheritDoc
     */
    public function create(DocumentInterface $document): OperationInterface
    {
        return $this->operationFactory->decorate(new CreateDoctrineDocumentOperation($this->documentManager, $document));
    }

    /**
     * @inheritDoc
     */
    public function update(DocumentInterface $newDocument, DocumentInterface $oldDocument): OperationInterface
    {
        return $this->operationFactory->decorate(
            new UpdateDoctrineDocumentOperation($this->documentManager, $newDocument, $oldDocument)
        );
    }

    /**
     * @inheritDoc
     */
    public function delete(DocumentInterface $document): OperationInterface
    {
        return $this->operationFactory->decorate(new DeleteDoctrineDocumentOperation($this->documentManager, $document));
    }

    /**
     * @inheritDoc
     */
    public function upsert(DocumentInterface $document): OperationInterface
    {
        return $this->operationFactory->decorate(new UpsertDoctrineDocumentOperation($this->documentManager, $document));
    }
}
