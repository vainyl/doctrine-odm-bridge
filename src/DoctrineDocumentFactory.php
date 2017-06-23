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

use Doctrine\Common\Persistence\Mapping\MappingException;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadataFactory;
use Vainyl\Core\ArrayInterface;
use Vainyl\Core\ArrayX\Factory\AbstractArrayFactory;
use Vainyl\Core\Hydrator\HydratorInterface;
use Vainyl\Doctrine\ODM\Exception\MissingDiscriminatorColumnException;
use Vainyl\Doctrine\ODM\Exception\UnknownDiscriminatorValueException;
use Vainyl\Document\Factory\DocumentFactoryInterface;

/**
 * Class DoctrineDocumentFactory
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class DoctrineDocumentFactory extends AbstractArrayFactory implements DocumentFactoryInterface
{
    private $hydrator;

    private $metadataFactory;

    /**
     * DoctrineDocumentFactory constructor.
     *
     * @param HydratorInterface    $hydrator
     * @param ClassMetadataFactory $metadataFactory
     */
    public function __construct(HydratorInterface $hydrator, ClassMetadataFactory $metadataFactory)
    {
        $this->hydrator = $hydrator;
        $this->metadataFactory = $metadataFactory;
    }

    /**
     * @inheritDoc
     */
    public function supports(string $name): bool
    {
        try {
            $this->metadataFactory->getMetadataFor($name);
        } catch (MappingException $e) {
            return false;
        }

        return true;
    }

    /**
     * @param array         $documentData
     * @param ClassMetadata $classMetadata
     *
     * @return string
     */
    public function getEntityName(array $documentData, ClassMetadata $classMetadata): string
    {
        if (ClassMetadata::INHERITANCE_TYPE_NONE === $classMetadata->inheritanceType) {
            return $classMetadata->name;
        }

        if (false === array_key_exists($classMetadata->discriminatorField, $documentData)) {
            throw new MissingDiscriminatorColumnException(
                $this,
                $classMetadata->discriminatorField,
                $documentData
            );
        }

        $discriminatorColumnValue = $documentData[$classMetadata->discriminatorField];
        if (false === array_key_exists($discriminatorColumnValue, $classMetadata->discriminatorMap)) {
            throw new UnknownDiscriminatorValueException(
                $this,
                $discriminatorColumnValue,
                $classMetadata->discriminatorMap
            );
        }

        return $classMetadata->discriminatorMap[$discriminatorColumnValue];
    }

    /**
     * @inheritDoc
     */
    public function doCreate(string $name, array $documentData = []): ArrayInterface
    {
        $documentName = $this->getEntityName($documentData, $this->metadataFactory->getMetadataFor($name));
        $document = $this->metadataFactory->getMetadataFor($documentName)->newInstance();

        return $this->hydrator->hydrate($document, $documentData);
    }
}