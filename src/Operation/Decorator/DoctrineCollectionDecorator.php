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

namespace Vainyl\Doctrine\ODM\Operation\Decorator;

use Vainyl\Core\ResultInterface;
use Vainyl\Doctrine\ODM\DoctrineDocumentManager;
use Vainyl\Operation\Collection\CollectionInterface;
use Vainyl\Operation\Collection\Decorator\AbstractCollectionDecorator;
use Vainyl\Operation\FailedOperationResult;

/**
 * Class DoctrineCollectionDecorator
 *
 * @author Nazar Ivenenko <nivanenko@gmail.com>
 */
class DoctrineCollectionDecorator extends AbstractCollectionDecorator
{
    private $documentManager;

    /**
     * DoctrineCollectionDecorator constructor.
     *
     * @param CollectionInterface $collection
     * @param DoctrineDocumentManager $documentManager
     */
    public function __construct(CollectionInterface $collection, DoctrineDocumentManager $documentManager)
    {
        $this->documentManager = $documentManager;
        parent::__construct($collection);
    }

    /**
     * @inheritDoc
     */
    public function execute(): ResultInterface
    {
        try {
            $this->documentManager->init();

            $result = parent::execute();
            if (false === $result->isSuccessful()) {
                return $result;
            }

            $this->documentManager->flush();
        } catch (\Exception $exception) {
            return new FailedOperationResult($this);
        }

        return $result;
    }
}
