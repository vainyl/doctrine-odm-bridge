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

use Vainyl\Doctrine\ODM\DoctrineDocumentManager;
use Vainyl\Operation\Collection\CollectionInterface;
use Vainyl\Operation\Collection\Decorator\AbstractCollectionFactoryDecorator;
use Vainyl\Operation\Collection\Factory\CollectionFactoryInterface;

/**
 * Class DoctrineCollectionFactoryDecorator
 *
 * @author Nazar Ivenenko <nivanenko@gmail.com>
 */
class DoctrineCollectionFactoryDecorator extends AbstractCollectionFactoryDecorator
{
    private $documentManager;

    /**
     * DoctrineCollectionFactoryDecorator constructor.
     *
     * @param CollectionFactoryInterface $collectionFactory
     * @param DoctrineDocumentManager    $documentManager
     */
    public function __construct(CollectionFactoryInterface $collectionFactory, DoctrineDocumentManager $documentManager)
    {
        $this->documentManager = $documentManager;
        parent::__construct($collectionFactory);
    }

    /**
     * @inheritDoc
     */
    public function create(array $operations = []): CollectionInterface
    {
        return new DoctrineCollectionDecorator(parent::create($operations), $this->documentManager);
    }
}
