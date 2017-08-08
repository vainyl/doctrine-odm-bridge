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

namespace Vainyl\Doctrine\ODM\Factory;

use Doctrine\ODM\MongoDB\Mapping\ClassMetadataFactory;
use Vainyl\Doctrine\ODM\DoctrineDocumentMetadata;

/**
 * Class DoctrineDocumentMetadataFactory
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class DoctrineDocumentMetadataFactory extends ClassMetadataFactory
{
    /**
     * @inheritDoc
     */
    protected function newClassMetadataInstance($className)
    {
        return new DoctrineDocumentMetadata($className);
    }
}