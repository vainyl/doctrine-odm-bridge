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

namespace Vain\Doctrine\Document\Operation\Factory;

use Doctrine\ODM\MongoDB\DocumentManager;
use Vain\Core\Document\DocumentInterface;
use Vain\Core\Event\Dispatcher\EventDispatcherInterface;
use Vain\Core\Event\Resolver\EventResolverInterface;
use Vain\Doctrine\Document\Operation\DoctrineCreateDocumentOperation;
use Vain\Doctrine\Document\Operation\DoctrineDeleteDocumentOperation;
use Vain\Doctrine\Document\Operation\DoctrineUpdateDocumentOperation;
use Vain\Core\Document\Operation\Factory\AbstractDocumentOperationFactory;
use Vain\Core\Document\Operation\Factory\DocumentOperationFactoryInterface;
use Vain\Core\Operation\Factory\OperationFactoryInterface;
use Vain\Core\Operation\OperationInterface;

/**
 * Class DoctrineDocumentOperationFactory
 *
 * @author Nazar Ivanenko <nivanenko@gmail.com>
 */
class DoctrineDocumentOperationFactory extends AbstractDocumentOperationFactory implements DocumentOperationFactoryInterface
{
    private $documentManager;

    private $eventResolver;

    private $eventDispatcher;

    /**
     * DoctrineDocumentOperationFactory constructor.
     *
     * @param OperationFactoryInterface $operationFactory
     * @param DocumentManager           $documentManager
     * @param EventResolverInterface    $eventResolver
     * @param EventDispatcherInterface  $eventDispatcher
     */
    public function __construct(
        OperationFactoryInterface $operationFactory,
        DocumentManager $documentManager,
        EventResolverInterface $eventResolver,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->documentManager = $documentManager;
        $this->eventResolver = $eventResolver;
        $this->eventDispatcher = $eventDispatcher;
        parent::__construct($operationFactory);
    }

    /**
     * @inheritDoc
     */
    public function createOperation(DocumentInterface $document) : OperationInterface
    {
        return $this->decorate(
            new DoctrineCreateDocumentOperation(
                $document,
                $this->documentManager,
                $this->eventResolver,
                $this->eventDispatcher
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function doUpdateOperation(DocumentInterface $newDocument, DocumentInterface $oldDocument) : OperationInterface
    {
        return $this->decorate(
            new DoctrineUpdateDocumentOperation(
                $newDocument,
                $oldDocument,
                $this->documentManager,
                $this->eventResolver,
                $this->eventDispatcher
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function deleteOperation(DocumentInterface $document) : OperationInterface
    {
        return $this->decorate(
            new DoctrineDeleteDocumentOperation(
                $document,
                $this->documentManager,
                $this->eventResolver,
                $this->eventDispatcher
            )
        );
    }
}
