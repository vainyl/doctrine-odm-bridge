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

use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Vainyl\Doctrine\Common\Metadata\DoctrineDomainMetadataInterface;

/**
 * Class DoctrineDocumentMetadata
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class DoctrineDocumentMetadata extends ClassMetadata implements DoctrineDomainMetadataInterface
{
    public $alias;

    public $scenarios;

    /**
     * @inheritDoc
     */
    public function __sleep()
    {
        return array_merge(parent::__sleep(), ['alias', 'scenarios']);
    }

    /**
     * @inheritDoc
     */
    public function setAlias(string $alias): DoctrineDomainMetadataInterface
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setScenarios(array $scenarios) : DoctrineDomainMetadataInterface
    {
        $this->scenarios = $scenarios;

        return $this;
    }
}