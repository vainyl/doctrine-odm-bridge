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

namespace Vainyl\Doctrine\ODM;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ManagerRegistry as DoctrineRegistryInterface;
use Doctrine\Common\Persistence\ObjectRepository as DoctrineRepositoryInterface;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadataFactory;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadataInfo;
use Doctrine\ODM\MongoDB\Mapping\MappingException;
use Doctrine\ODM\MongoDB\Types\Type;
use Vainyl\Core\ArrayInterface;
use Vainyl\Core\Hydrator\AbstractHydrator;
use Vainyl\Core\Hydrator\HydratorInterface;
use Vainyl\Document\DocumentInterface;

/**
 * Class DoctrineDocumentHydrator
 *
 * @author Nazar Ivanenko <nivanenko@gmail.com>
 */
class DoctrineDocumentHydrator extends AbstractHydrator implements HydratorInterface
{
    private $metadataFactory;

    private $doctrineRegistry;

    /**
     * DoctrineDocumentHydrator constructor.
     *
     * @param ClassMetadataFactory      $metadataFactory
     * @param DoctrineRegistryInterface $doctrineRegistry
     */
    public function __construct(ClassMetadataFactory $metadataFactory, DoctrineRegistryInterface $doctrineRegistry)
    {
        $this->metadataFactory = $metadataFactory;
        $this->doctrineRegistry = $doctrineRegistry;
    }

    /**
     * @inheritDoc
     */
    public function supports($class): bool
    {
        try {
            $this->metadataFactory->getMetadataFor(get_class($class));
        } catch (MappingException $e) {
            return false;
        }

        return true;
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
    public function doHydrate($document, array $documentData): ArrayInterface
    {
        /**
         * @var ClassMetadataInfo $classMetadata
         * @var DocumentInterface $document
         */
        $classMetadata = $this->metadataFactory->getMetadataFor(get_class($document));

        foreach ($documentData as $field => $value) {
            switch (true) {
                case array_key_exists($field, $classMetadata->fieldMappings):
                    $fieldMapping = $classMetadata->fieldMappings[$field];
                    $classMetadata->reflFields[$fieldMapping['fieldName']]
                        ->setValue($document, Type::getType($fieldMapping['type'])->convertToPHPValue($value));
                    break;
                case array_key_exists($field, $classMetadata->associationMappings):
                    $associationMapping = $classMetadata->associationMappings[$field];
                    $referenceEntity = $associationMapping['targetEntity'];
                    switch ($associationMapping['type']) {
                        case ClassMetadataInfo::REFERENCE_ONE:
                            $classMetadata->reflFields[$associationMapping['fieldName']]
                                ->setValue(
                                    $document,
                                    $this->getRepository($referenceEntity)->find($value)
                                );

                            break;
                        case ClassMetadataInfo::REFERENCE_MANY:
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
                        case ClassMetadataInfo::EMBED_ONE:
                            break;
                        case ClassMetadataInfo::EMBED_MANY:
                            break;
                    }
            }
        }

        return $document;
    }
}
