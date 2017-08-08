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

namespace Vainyl\Doctrine\ODM\Operation;

use Doctrine\Common\Persistence\ObjectManager as DocumentManagerInterface;
use Vainyl\Core\ResultInterface;
use Vainyl\Document\DocumentInterface;
use Vainyl\Operation\AbstractOperation;
use Vainyl\Operation\SuccessfulOperationResult;

/**
 * Class UpdateDoctrineDocumentOperation
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class UpdateDoctrineDocumentOperation extends AbstractOperation
{
    private $newDocument;

    private $oldDocument;

    private $documentManager;

    /**
     * UpdateDoctrineDocumentOperation constructor.
     *
     * @param DocumentManagerInterface $documentManager
     * @param DocumentInterface        $newDocument
     * @param DocumentInterface        $oldDocument
     */
    public function __construct(
        DocumentManagerInterface $documentManager,
        DocumentInterface $newDocument,
        DocumentInterface $oldDocument
    ) {
        $this->newDocument = $newDocument;
        $this->oldDocument = $oldDocument;
        $this->documentManager = $documentManager;
    }

    /**
     * @inheritDoc
     */
    public function getNewDocument(): DocumentInterface
    {
        return $this->newDocument;
    }

    /**
     * @inheritDoc
     */
    public function getOldDocument(): DocumentInterface
    {
        return $this->oldDocument;
    }

    /**
     * @inheritDoc
     */
    public function execute(): ResultInterface
    {
        return new SuccessfulOperationResult($this);
    }
}
