<?php
/**
 * Vainyl
 *
 * PHP Version 7
 *
 * @package   Doctrine-ODM-Bridge-orm-bridge
 * @license   https://opensource.org/licenses/MIT MIT License
 * @link      https://vainyl.com
 */
declare(strict_types=1);

namespace Vainyl\Doctrine\ODM\Factory;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriver;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Doctrine\ODM\MongoDB\Mapping\Driver\SimplifiedYamlDriver;
use Doctrine\ODM\MongoDB\Mapping\Driver\XmlDriver;
use Vainyl\Core\AbstractIdentifiable;
use Vainyl\Doctrine\Common\Driver\Provider\DoctrineMappingPathProvider;
use Vainyl\Doctrine\ODM\DoctrineDocumentSettings;
use Vainyl\Doctrine\ODM\Exception\UnknownDoctrineConfigTypeException;

/**
 * Class DoctrineDocumentMappingDriverFactory
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class DoctrineDocumentMappingDriverFactory extends AbstractIdentifiable
{
    private $pathProvider;

    /**
     * DoctrineDocumentMappingDriverFactory constructor.
     *
     * @param DoctrineMappingPathProvider $pathProvider
     */
    public function __construct(DoctrineMappingPathProvider $pathProvider)
    {
        $this->pathProvider = $pathProvider;
    }

    /**
     * @param DoctrineDocumentSettings $settings
     *
     * @return MappingDriver
     */
    public function create(DoctrineDocumentSettings $settings): MappingDriver
    {
        $paths = $this->pathProvider->getPaths($settings);

        switch ($settings->getDriverName()) {
            case 'yaml':
                return new SimplifiedYamlDriver($paths, $settings->getFileExtension());
                break;
            case 'xml':
                return new XmlDriver($paths, $settings->getFileExtension());
                break;
            case 'annotation':
                return new AnnotationDriver(new AnnotationReader(), $paths);
                break;
            default:
                throw new UnknownDoctrineConfigTypeException($this, $settings->getDriverName());
        }
    }
}