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
use Doctrine\Common\Persistence\Mapping\MappingException;
use Doctrine\Common\Persistence\ObjectManager as DoctrineManagerInterface;
use Doctrine\Common\Persistence\ObjectRepository as DoctrineRepositoryInterface;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadataFactory;
use Doctrine\ODM\MongoDB\PersistentCollection;
use Doctrine\ODM\MongoDB\Types\Type;
use Vainyl\Core\ArrayInterface;
use Vainyl\Core\Hydrator\AbstractHydrator;
use Vainyl\Doctrine\ODM\Exception\MissingDiscriminatorColumnException;
use Vainyl\Doctrine\ODM\Exception\UnknownDiscriminatorValueException;
use Vainyl\Doctrine\ODM\Exception\UnknownReferenceEntityException;
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
     * @return DoctrineManagerInterface
     */
    public function getDocumentManager(): DoctrineManagerInterface
    {
        return $this->doctrineRegistry->getManager('document');
    }

    /**
     * @param string $className
     *
     * @return DoctrineRepositoryInterface
     */
    public function getRepository(string $className): DoctrineRepositoryInterface
    {
        return $this->getDocumentManager()->getRepository($className);
    }

    /**
     * @param array $mapping
     *
     * @return PersistentCollection
     */
    public function getPersistentCollection(array $mapping): PersistentCollection
    {
        return $this->getDocumentManager()
                    ->getConfiguration()
                    ->getPersistentCollectionFactory()
                    ->create($this->getDocumentManager(), $mapping);
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
                    switch ($associationMapping['association']) {
                        case ClassMetadata::REFERENCE_ONE:
                            if (null === ($processedValue = $this->getRepository($referenceEntity)->find($value))) {
                                throw new UnknownReferenceEntityException($this, $referenceEntity, $value);
                            }
                            $reflectionField = $classMetadata->reflFields[$associationMapping['fieldName']];
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
                            $reflectionField = $classMetadata->reflFields[$associationMapping['fieldName']];
                            break;
                        case ClassMetadata::EMBED_ONE:
                            $reflectionField = $classMetadata->reflFields[$associationMapping['fieldName']];
                            $processedValue = 1;
                            break;
                        case ClassMetadata::EMBED_MANY:
                            $reflectionField = $classMetadata->reflFields[$associationMapping['fieldName']];
                            $processedValue = 2;
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
    public function doUpdate($object, array $data): ArrayInterface
    {
        trigger_error('Method doUpdate is not implemented', E_USER_ERROR);
    }
}