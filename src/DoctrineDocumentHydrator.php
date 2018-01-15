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

namespace Vainyl\Doctrine\ODM;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\Mapping\MappingException;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadataFactory;
use Doctrine\ODM\MongoDB\Types\Type;
use Vainyl\Core\Hydrator\AbstractHydrator;
use Vainyl\Core\Hydrator\Registry\HydratorRegistryInterface;
use Vainyl\Core\IdentifiableInterface;
use Vainyl\Doctrine\ODM\Exception\MissingDiscriminatorColumnException;
use Vainyl\Doctrine\ODM\Exception\UnknownDiscriminatorValueException;
use Vainyl\Doctrine\ODM\Exception\UnknownReferenceEntityException;
use Vainyl\Document\DocumentInterface;
use Vainyl\Domain\Hydrator\DomainHydratorInterface;
use Vainyl\Domain\Storage\DomainStorageInterface;

/**
 * Class DoctrineDocumentHydrator
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 * @author Andrii Dembitskiy <andrew.dembitskiy@gmail.com>
 */
class DoctrineDocumentHydrator extends AbstractHydrator implements DomainHydratorInterface
{
    private $hydratorRegistry;

    private $domainStorage;

    private $metadataFactory;

    /**
     * DoctrineDocumentHydrator constructor.
     *
     * @param HydratorRegistryInterface $registry
     * @param DomainStorageInterface    $domainStorage
     * @param ClassMetadataFactory      $metadataFactory
     */
    public function __construct(
        HydratorRegistryInterface $registry,
        DomainStorageInterface $domainStorage,
        ClassMetadataFactory $metadataFactory
    ) {
        $this->hydratorRegistry = $registry;
        $this->domainStorage    = $domainStorage;
        $this->metadataFactory  = $metadataFactory;
    }

    /**
     * @inheritDoc
     */
    public function doCreate(string $name, array $documentData = []): IdentifiableInterface
    {
        /**
         * @var ClassMetadata     $classMetadata
         * @var DocumentInterface $document
         */
        $documentName  = $this->getDocumentName($documentData, $this->metadataFactory->getMetadataFor($name));
        $classMetadata = $this->metadataFactory->getMetadataFor($documentName);
        $document      = $classMetadata->newInstance();

        $missedAssociations = $classMetadata->associationMappings;

        foreach ($documentData as $field => $value) {
            $reflectionField = $processedValue = null;
            switch (true) {
                case $classMetadata->hasAssociation($field):
                    $associationMapping = $classMetadata->associationMappings[$field];
                    $referenceEntity    = $classMetadata->getAssociationTargetClass($field);
                    $reflectionField    = $classMetadata->getReflectionProperty($associationMapping['fieldName']);

                    unset($missedAssociations[$field]);

                    switch ($associationMapping['association']) {
                        case ClassMetadata::REFERENCE_ONE:
                            if (null === ($processedValue = $this->domainStorage->findOne($referenceEntity, $value))) {
                                throw new UnknownReferenceEntityException($this, $referenceEntity, $value);
                            }
                            break;
                        case ClassMetadata::REFERENCE_MANY:
                            $processedValue = new ArrayCollection();
                            foreach ($value as $referenceData) {
                                if (null === ($reference = $this->domainStorage->findOne(
                                        $referenceEntity,
                                        $referenceData
                                    ))) {
                                    throw new UnknownReferenceEntityException($this, $referenceEntity, $referenceData);
                                }
                                $processedValue->add($reference);
                            }
                            break;
                        case ClassMetadata::EMBED_ONE:
                            $processedValue = $this->hydratorRegistry->getHydrator($referenceEntity)->create(
                                $referenceEntity,
                                $value
                            );
                            break;
                        case ClassMetadata::EMBED_MANY:
                            $processedValue = new ArrayCollection();
                            foreach ($value as $singleDocument) {
                                $processedValue[] = $this->hydratorRegistry->getHydrator($referenceEntity)->create(
                                    $referenceEntity,
                                    $singleDocument
                                );
                            }
                            break;
                        default:
                            break;
                    }
                    break;
                case $classMetadata->hasField($field):
                    $fieldMapping    = $classMetadata->getFieldMapping($field);
                    $processedValue  = Type::getType($classMetadata->getTypeOfField($field))->convertToPHPValue($value);
                    $reflectionField = $classMetadata->getReflectionProperty($fieldMapping['fieldName']);

                    break;
                default:
                    break;
            }
            if (null !== $reflectionField) {
                $reflectionField->setValue($document, $processedValue);
            }
        }

        $this->fillEmptyCollectionOnManyAssociations($missedAssociations, $classMetadata, $document);

        return $document;
    }

    /**
     * @inheritDoc
     */
    public function doUpdate($document, array $documentData): IdentifiableInterface
    {
        /**
         * @var ClassMetadata     $classMetadata
         * @var DocumentInterface $document
         * @var DocumentInterface $newDocument
         */
        $classMetadata = $this->metadataFactory->getMetadataFor(get_class($document));

        foreach ($documentData as $field => $value) {
            $reflectionField = $processedValue = null;
            switch (true) {
                case $classMetadata->hasAssociation($field):
                    $associationMapping = $classMetadata->associationMappings[$field];
                    $referenceEntity    = $classMetadata->getAssociationTargetClass($field);
                    $reflectionField    = $classMetadata->getReflectionProperty($associationMapping['fieldName']);

                    switch ($associationMapping['association']) {
                        case ClassMetadata::REFERENCE_ONE:
                            if ($value === null) {
                                break;
                            }

                            if (null === ($processedValue = $this->domainStorage->findOne($referenceEntity, $value))) {
                                throw new UnknownReferenceEntityException($this, $referenceEntity, $value);
                            }
                            break;
                        case ClassMetadata::REFERENCE_MANY:
                            $processedValue = new ArrayCollection();
                            foreach ($value as $referenceData) {
                                if (null === ($reference = $this->domainStorage->findOne(
                                        $referenceEntity,
                                        $referenceData
                                    ))) {
                                    throw new UnknownReferenceEntityException($this, $referenceEntity, $referenceData);
                                }
                                $processedValue->add($reference);
                            }
                            break;
                        case ClassMetadata::EMBED_ONE:
                            $processedValue = $this->hydratorRegistry->getHydrator($referenceEntity)->create(
                                $referenceEntity,
                                $value
                            );
                            break;
                        case ClassMetadata::EMBED_MANY:
                            $processedValue = new ArrayCollection();
                            foreach ($value as $singleDocument) {
                                $processedValue[] = $this->hydratorRegistry->getHydrator($referenceEntity)->create(
                                    $referenceEntity,
                                    $singleDocument
                                );
                            }
                            break;
                        default:
                            break;
                    }
                    break;
                case $classMetadata->hasField($field):
                    $fieldMapping    = $classMetadata->getFieldMapping($field);
                    $processedValue  = Type::getType($classMetadata->getTypeOfField($field))->convertToPHPValue($value);
                    $reflectionField = $classMetadata->getReflectionProperty($fieldMapping['fieldName']);

                    break;
                default:
                    break;
            }
            if (null !== $reflectionField) {
                $reflectionField->setValue($document, $processedValue);
            }
        }

        return $document;
    }

    /**
     * @param array         $documentData
     * @param ClassMetadata $classMetadata
     *
     * @return string
     * @throws MissingDiscriminatorColumnException
     * @throws UnknownDiscriminatorValueException
     */
    private function getDocumentName(array $documentData, ClassMetadata $classMetadata): string
    {
        if (ClassMetadata::INHERITANCE_TYPE_NONE === $classMetadata->inheritanceType
            ||
            null !== $classMetadata->discriminatorValue
        ) {
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
        if (false === array_key_exists($discriminatorColumnValue, $classMetadata->discriminatorMap)
            &&
            null === ($discriminatorColumnValue = $classMetadata->defaultDiscriminatorValue)
        ) {
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
     * @param array         $missedAssociations
     * @param ClassMetadata $classMetadata
     * @param               $document
     */
    private function fillEmptyCollectionOnManyAssociations(
        array $missedAssociations,
        ClassMetadata $classMetadata,
        $document
    ): void {
        foreach ($missedAssociations as $missedAssociationMeta) {
            switch ($missedAssociationMeta['association']) {
                case ClassMetadata::REFERENCE_MANY:
                case ClassMetadata::EMBED_MANY:
                    $classMetadata->setFieldValue(
                        $document,
                        $missedAssociationMeta['fieldName'],
                        new ArrayCollection()
                    );
                    break;
            }
        }
    }
}
