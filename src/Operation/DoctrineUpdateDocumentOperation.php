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

namespace Vain\Doctrine\Document\Operation;

use Doctrine\ODM\MongoDB\DocumentManager;
use Vain\Core\Document\DocumentInterface;
use Vain\Core\Document\Operation\AbstractUpdateDocumentOperation;
use Vain\Core\Event\Dispatcher\EventDispatcherInterface;
use Vain\Core\Event\Resolver\EventResolverInterface;

/**
 * Class DoctrineUpdateDocumentOperation
 *
 * @author Nazar Ivanenko <nivanenko@gmail.com>
 */
class DoctrineUpdateDocumentOperation extends AbstractUpdateDocumentOperation
{
    private $newDocument;

    private $oldDocument;

    private $documentManager;

    /**
     * DoctrineUpdateDocumentOperation constructor.
     *
     * @param DocumentInterface        $newDocument
     * @param DocumentInterface        $oldDocument
     * @param DocumentManager          $documentManager
     * @param EventResolverInterface   $eventResolver
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        DocumentInterface $newDocument,
        DocumentInterface $oldDocument,
        DocumentManager $documentManager,
        EventResolverInterface $eventResolver,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->newDocument = $newDocument;
        $this->oldDocument = $oldDocument;
        $this->documentManager = $documentManager;
        parent::__construct($eventResolver, $eventDispatcher);
    }

    /**
     * @inheritDoc
     */
    public function getNewDocument() : DocumentInterface
    {
        return $this->newDocument;
    }

    /**
     * @inheritDoc
     */
    public function getOldDocument() : DocumentInterface
    {
        return $this->oldDocument;
    }

    /**
     * @inheritDoc
     */
    public function updateDocument(DocumentInterface $newDocument, DocumentInterface $oldDocument) : DocumentInterface
    {
        $this->documentManager->persist($newDocument);

        return $newDocument;
    }
}
