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
use Vain\Core\Document\Operation\AbstractCreateDocumentOperation;
use Vain\Core\Event\Dispatcher\EventDispatcherInterface;
use Vain\Core\Event\Resolver\EventResolverInterface;

/**
 * Class DoctrineCreateDocumentOperation
 *
 * @author Nazar Ivanenko <nivanenko@gmail.com>
 */
class DoctrineCreateDocumentOperation extends AbstractCreateDocumentOperation
{
    private $document;

    private $documentManager;

    /**
     * DoctrineCreateDocumentOperation constructor.
     *
     * @param DocumentInterface        $document
     * @param DocumentManager          $documentManager
     * @param EventResolverInterface   $eventResolver
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        DocumentInterface $document,
        DocumentManager $documentManager,
        EventResolverInterface $eventResolver,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->document = $document;
        $this->documentManager = $documentManager;
        parent::__construct($eventResolver, $eventDispatcher);
    }

    /**
     * @inheritDoc
     */
    public function createDocument() : DocumentInterface
    {
        $this->documentManager->persist($this->document);

        return $this->document;
    }
}
