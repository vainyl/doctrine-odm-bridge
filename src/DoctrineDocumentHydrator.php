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
use Doctrine\Common\Persistence\Mapping\MappingException;
use Doctrine\Common\Persistence\ObjectManager as DoctrineManagerInterface;
use Doctrine\Common\Persistence\ObjectRepository as DoctrineRepositoryInterface;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadataFactory;
use Doctrine\ODM\MongoDB\PersistentCollection;
use Doctrine\ODM\MongoDB\Types\Type;
use Vainyl\Core\ArrayInterface;
use Vainyl\Core\Hydrator\AbstractHydrator;
use Vainyl\Core\Hydrator\HydratorInterface;
use Vainyl\Core\Hydrator\Registry\HydratorRegistryInterface;
use Vainyl\Doctrine\ODM\Exception\MissingDiscriminatorColumnException;
use Vainyl\Doctrine\ODM\Exception\UnknownDiscriminatorValueException;
use Vainyl\Doctrine\ODM\Exception\UnknownReferenceEntityException;
use Vainyl\Document\DocumentInterface;
use Vainyl\Domain\Hydrator\DomainHydratorInterface;

/**
 * Class DoctrineDocumentHydrator
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class DoctrineDocumentHydrator extends AbstractHydrator implements DomainHydratorInterface
{
    private $hydratorRegistry;

    private $manager;

    private $metadataFactory;

    /**
     * DoctrineDocumentHydrator constructor.
     *
     * @param HydratorRegistryInterface $registry
     * @param DoctrineManagerInterface  $manager
     * @param ClassMetadataFactory      $metadataFactory
     */
    public function __construct(
        HydratorRegistryInterface $registry,
        DoctrineManagerInterface $manager,
        ClassMetadataFactory $metadataFactory
    ) {
        $this->hydratorRegistry = $registry;
        $this->manager = $manager;
        $this->metadataFactory = $metadataFactory;
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

        foreach ($documentData as $field => $value) {
            $reflectionField = $processedValue = null;
            switch (true) {
                case array_key_exists($field, $classMetadata->associationMappings):
                    $associationMapping = $classMetadata->associationMappings[$field];
                    $referenceEntity = $associationMapping['targetDocument'];
                    $reflectionField = $classMetadata->reflFields[$associationMapping['fieldName']];
                    switch ($associationMapping['association']) {
                        case ClassMetadata::REFERENCE_ONE:
                            if (null === ($processedValue = $this->getRepository($referenceEntity)->find($value))) {
                                throw new UnknownReferenceEntityException($this, $referenceEntity, $value);
                            }
                            break;
                        case ClassMetadata::REFERENCE_MANY:
                            $processedValue = new ArrayCollection();
                            $repository = $this->getRepository($referenceEntity);
                            foreach ($value as $referenceData) {
                                if (null === ($reference = $repository->find($referenceData))) {
                                    throw new UnknownReferenceEntityException($this, $referenceEntity, $referenceData);
                                }
                                $processedValue->add($reference);
                            }
                            break;
                        case ClassMetadata::EMBED_ONE:
                            $processedValue = $this->create($referenceEntity, $value);
                            break;
                        case ClassMetadata::EMBED_MANY:
                            $processedValue = [];
                            foreach ($value as $singleDocument) {
                                $processedValue[] = $this->create($referenceEntity, $singleDocument);
                            }
                            break;
                    }
                    break;
                case array_key_exists($field, $classMetadata->fieldMappings):
                    $fieldMapping = $classMetadata->fieldMappings[$field];
                    $processedValue = Type::getType($fieldMapping['type'])->convertToPHPValue($value);
                    $reflectionField = $classMetadata->reflFields[$fieldMapping['fieldName']];

                    break;
            }
            if (null !== $reflectionField) {
                $reflectionField->setValue($document, $processedValue);
            }
        }

        return $document;
    }

    /**
     * @inheritDoc
     */
    public function doUpdate($document, array $documentData): ArrayInterface
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
                case array_key_exists($field, $classMetadata->associationMappings):
                    $associationMapping = $classMetadata->associationMappings[$field];
                    $referenceEntity = $associationMapping['targetDocument'];
                    $reflectionField = $classMetadata->reflFields[$associationMapping['fieldName']];
                    switch ($associationMapping['association']) {
                        case ClassMetadata::EMBED_ONE:
                            $processedValue = $this->create($referenceEntity, $value);
                            break;
                        case ClassMetadata::EMBED_MANY:
                            $processedValue = [];
                            foreach ($value as $singleDocument) {
                                $processedValue[] = $this->create($referenceEntity, $singleDocument);
                            }
                            break;
                    }
                    break;
                case array_key_exists($field, $classMetadata->fieldMappings):
                    $fieldMapping = $classMetadata->fieldMappings[$field];
                    $processedValue = Type::getType($fieldMapping['type'])->convertToPHPValue($value);
                    $reflectionField = $classMetadata->reflFields[$fieldMapping['fieldName']];

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
     * @return HydratorInterface
     */
    public function getHydrator(string $className): HydratorInterface
    {
        return $this->hydratorRegistry->getHydrator($className);
    }

    /**
     * @param array $mapping
     *
     * @return PersistentCollection
     */
    public function getPersistentCollection(array $mapping): PersistentCollection
    {
        return $this->manager
            ->getConfiguration()
            ->getPersistentCollectionFactory()
            ->create($this->manager, $mapping);
    }

    /**
     * @param string $className
     *
     * @return DoctrineRepositoryInterface
     */
    public function getRepository(string $className): DoctrineRepositoryInterface
    {
        return $this->manager->getRepository($className);
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
}