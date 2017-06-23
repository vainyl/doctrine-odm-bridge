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

namespace Vainyl\Doctrine\ODM\Operation;

use Doctrine\Common\Persistence\ObjectManager as DocumentManagerInterface;
use Vainyl\Core\ResultInterface;
use Vainyl\Document\DocumentInterface;
use Vainyl\Operation\AbstractOperation;
use Vainyl\Operation\SuccessfulOperationResult;

/**
 * Class DeleteDoctrineDocumentOperation
 *
 * @author Nazar Ivanenko <nivanenko@gmail.com>
 */
class DeleteDoctrineDocumentOperation extends AbstractOperation
{
    private $document;

    private $documentManager;

    /**
     * CreateDoctrineDocumentOperation constructor.
     *
     * @param DocumentManagerInterface $documentManager
     * @param DocumentInterface        $document
     */
    public function __construct(
        DocumentManagerInterface $documentManager,
        DocumentInterface $document
    ) {
        $this->document = $document;
        $this->documentManager = $documentManager;
    }

    /**
     * @inheritDoc
     */
    public function execute(): ResultInterface
    {
        $this->documentManager->remove($this->document);

        return new SuccessfulOperationResult($this);
    }
}
