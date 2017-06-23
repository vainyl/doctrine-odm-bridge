<?php
/**
 * Vainyl
 *
 * PHP Version 7
 *
 * @package   Doctrine-odm-bridge
 * @license   https://opensource.org/licenses/MIT MIT License
 * @link      https://vainyl.com
 */
declare(strict_types=1);

namespace Vainyl\Doctrine\ODM;

use Vainyl\Core\AbstractIdentifiable;
use Vainyl\Core\ArrayInterface;
use Vainyl\Document\Factory\DocumentFactoryInterface;

/**
 * Class DoctrineDocumentFactory
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class DoctrineDocumentFactory extends AbstractIdentifiable implements DocumentFactoryInterface
{
    private $hydratorStorage;

    /**
     * DoctrineDocumentFactory constructor.
     *
     * @param \ArrayAccess $hydratorStorage
     */
    public function __construct(\ArrayAccess $hydratorStorage)
    {
        $this->hydratorStorage = $hydratorStorage;
    }

    /**
     * @inheritDoc
     */
    public function supports(string $name): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function create(string $name, array $data = []): ArrayInterface
    {
        trigger_error('Method create is not implemented', E_USER_ERROR);
    }
}