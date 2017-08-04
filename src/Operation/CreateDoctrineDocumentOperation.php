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
 * Class CreateDoctrineDocumentOperation
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class CreateDoctrineDocumentOperation extends AbstractOperation
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
        $this->documentManager->persist($this->document);

        return new SuccessfulOperationResult($this);
    }
}
