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

namespace Vainyl\Doctrine\ODM\Exception;

use Vainyl\Core\Exception\AbstractHydratorException;
use Vainyl\Core\Hydrator\HydratorInterface;

/**
 * Class UnknownDiscriminatorValueException
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class UnknownDiscriminatorValueException extends AbstractHydratorException
{
    private $value;

    private $discriminatorMap;

    /**
     * UnknownDiscriminatorValueException constructor.
     *
     * @param HydratorInterface $hydrator
     * @param string            $value
     * @param array             $discriminatorMap
     */
    public function __construct(HydratorInterface $hydrator, $value, array $discriminatorMap)
    {
        $this->value = $value;
        $this->discriminatorMap = $discriminatorMap;
        parent::__construct(
            $hydrator,
            sprintf(
                'Value %s is not found in discriminator map %s',
                $value,
                json_encode($discriminatorMap)
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return array_merge(
            ['value' => $this->value, 'discriminator_map' => $this->discriminatorMap],
            parent::toArray()
        );
    }
}