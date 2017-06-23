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

use Vainyl\Core\ArrayInterface;
use Vainyl\Core\ArrayX\Factory\AbstractArrayFactory;
use Vainyl\Document\Factory\DocumentFactoryInterface;

/**
 * Class DoctrineDocumentFactory
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class DoctrineDocumentFactory extends AbstractArrayFactory implements DocumentFactoryInterface
{
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
    public function doCreate(string $name, array $data = []): ArrayInterface
    {
        return null;
    }
}