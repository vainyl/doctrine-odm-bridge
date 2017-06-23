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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ManagerRegistry as DoctrineRegistryInterface;
use Doctrine\Common\Persistence\ObjectRepository as DoctrineRepositoryInterface;
use Doctrine\Common\Persistence\Mapping\MappingException;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadataFactory;
use Doctrine\ODM\MongoDB\Types\Type;
use Vainyl\Core\ArrayInterface;
use Vainyl\Core\Hydrator\AbstractHydrator;
use Vainyl\Doctrine\ODM\Exception\MissingDiscriminatorColumnException;
use Vainyl\Doctrine\ODM\Exception\UnknownDiscriminatorValueException;
use Vainyl\Document\DocumentInterface;

/**
 * Class DoctrineDocumentHydrator
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class DoctrineDocumentHydrator extends AbstractHydrator
{
    private $doctrineRegistry;

    private $metadataFactory;

    /**
     * DoctrineDocumentHydrator constructor.
     *
     * @param DoctrineRegistryInterface $doctrineRegistry
     * @param ClassMetadataFactory      $metadataFactory
     */
    public function __construct(DoctrineRegistryInterface $doctrineRegistry, ClassMetadataFactory $metadataFactory)
    {
        $this->doctrineRegistry = $doctrineRegistry;
        $this->metadataFactory = $metadataFactory;
    }

    /**
     * @inheritDoc
     */
    public function supports(string $className): bool
    {
        try {
            $this->metadataFactory->getMetadataFor($className);
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
    public function getDocumentName(array $documentData, ClassMetadata $classMetadata): string
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
     * @param string $className
     *
     * @return DoctrineRepositoryInterface
     */
    public function getRepository(string $className): DoctrineRepositoryInterface
    {
        return $this->doctrineRegistry->getRepository($className, 'document');
    }

    /**
     * @inheritDoc
     */
    public function doCreate(string $name, array $documentData = []): ArrayInterface
    {
        /**
         * @var ClassMetadata     $classMetadata
         * @var DocumentInterface $document
         */
        $documentName = $this->getDocumentName($documentData, $this->metadataFactory->getMetadataFor($name));
        $classMetadata = $this->metadataFactory->getMetadataFor($documentName);
        $document = $classMetadata->newInstance();

        $classMetadata = $this->metadataFactory->getMetadataFor(get_class($document));

        foreach ($documentData as $field => $value) {
            switch (true) {
                case array_key_exists($field, $classMetadata->associationMappings):
                    $associationMapping = $classMetadata->associationMappings[$field];
                    $referenceEntity = $associationMapping['targetDocument'];
                    switch ($associationMapping['association']) {
                        case ClassMetadata::REFERENCE_ONE:
                            $classMetadata->reflFields[$associationMapping['fieldName']]
                                ->setValue(
                                    $document,
                                    $this->getRepository($referenceEntity)->find($value)
                                );

                            break;
                        case ClassMetadata::REFERENCE_MANY:
                            $collection = new ArrayCollection();
                            $repository = $this->getRepository($referenceEntity);
                            foreach ($value as $referenceData) {
                                $collection->add($repository->find($referenceData));
                            }
                            $classMetadata->reflFields[$associationMapping['fieldName']]
                                ->setValue(
                                    $document,
                                    $collection
                                );

                            break;
                        case ClassMetadata::EMBED_ONE:
                            break;
                        case ClassMetadata::EMBED_MANY:
                            break;
                    }
                    break;
                case array_key_exists($field, $classMetadata->fieldMappings):
                    $fieldMapping = $classMetadata->fieldMappings[$field];
                    $classMetadata->reflFields[$fieldMapping['fieldName']]
                        ->setValue($document, Type::getType($fieldMapping['type'])->convertToPHPValue($value));
                    break;
            }
        }

        return $document;
    }

    /**
     * @inheritDoc
     */
    public function doUpdate($object, array $data): ArrayInterface
    {
        trigger_error('Method doUpdate is not implemented', E_USER_ERROR);
    }
}