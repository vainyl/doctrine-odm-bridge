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

use Doctrine\Common\Persistence\ManagerRegistry as DoctrineRegistryInterface;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadataFactory;
use Doctrine\ODM\MongoDB\Mapping\MappingException;
use Vainyl\Core\ArrayInterface;
use Vainyl\Core\Hydrator\AbstractHydrator;
use Vainyl\Core\Hydrator\HydratorInterface;

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
     * @inheritDoc
     */
    public function doHydrate($object, array $documentData): ArrayInterface
    {
        return $object;
//        $class = $this->documentManager->getClassMetadata($name);
//
//        $discriminatorValue = null;
//        if (isset($class->discriminatorField, $data[$class->discriminatorField])) {
//            $discriminatorValue = $documentData[$class->discriminatorField];
//        } elseif (isset($class->defaultDiscriminatorValue)) {
//            $discriminatorValue = $class->defaultDiscriminatorValue;
//        }
//
//        if ($discriminatorValue !== null) {
//            $className = isset($class->discriminatorMap[$discriminatorValue])
//                ? $class->discriminatorMap[$discriminatorValue]
//                : $discriminatorValue;
//
//            $class = $this->dm->getClassMetadata($className);
//
//            unset($documentData[$class->discriminatorField]);
//        }
//
//        $document = $class->newInstance();
//        $this->documentManager->getHydratorFactory()->hydrate($document, $documentData);
//
//        return $document;
    }

//    /**
//     * @inheritDoc
//     */
//    public function updateDocument(DocumentInterface $document, array $documentData): DocumentInterface
//    {
//        $documentName = get_class($document);
//        $classMetadata = $this->getClassMetadata($documentName);
//        $associationOriginals = [];
//        foreach ($classMetadata->associationMappings as $fieldName => $mapping) {
//            if (isset($mapping['discriminatorField'])) {
//                $discriminatorField = $mapping['discriminatorField'];
//                if (!isset($documentData[$fieldName])) {
//                    $documentData[$fieldName] = [];
//                }
//                if (!isset($documentData[$fieldName][$discriminatorField])) {
//                    $assosiation = $classMetadata->reflFields[$fieldName]->getValue($document);
//                    $associationOriginals[$fieldName] = $assosiation;
//                    $documentData[$fieldName][$discriminatorField] = $this->getClassMetadata(
//                        get_class($assosiation)
//                    )->reflFields[$discriminatorField]->getValue($assosiation);
//                }
//            }
//        }
//        try {
//            $data = $this->documentManager->getHydratorFactory()->getHydratorFor($documentName)->hydrate(
//                $document,
//                $documentData
//            );
//        } catch (MappingException $me) {
//            throw new DocumentMappingException($this, $document, $me);
//        }
//        $uow = $this->documentManager->getUnitOfWork();
//        foreach ($associationOriginals as $fieldName => $assosiation) {
//            $uow->setOriginalDocumentData(
//                $classMetadata->reflFields[$fieldName]->getValue($document),
//                $assosiation->toArray()
//            );
//        }
//        if ($document instanceof Proxy) {
//            $document->__isInitialized__ = true;
//            $document->__setInitializer(null);
//            $document->__setCloner(null);
//            // lazy properties may be left uninitialized
//            $properties = $document->__getLazyProperties();
//            foreach ($properties as $propertyName => $property) {
//                if (!isset($document->$propertyName)) {
//                    $document->$propertyName = $properties[$propertyName];
//                }
//            }
//        }
//
//        return $document;
//    }
}
