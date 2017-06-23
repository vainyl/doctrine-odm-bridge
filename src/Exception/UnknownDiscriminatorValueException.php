<?php
/**
 * Vainyl
 *
 * PHP Version 7
 *
 * @package   Doctrine-orm-bridge
 * @license   https://opensource.org/licenses/MIT MIT License
 * @link      https://vainyl.com
 */
declare(strict_types=1);

namespace Vainyl\Doctrine\ODM\Exception;

use Vainyl\Core\Exception\AbstractArrayFactoryException;
use Vainyl\Document\Factory\DocumentFactoryInterface;

/**
 * Class UnknownDiscriminatorValueException
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class UnknownDiscriminatorValueException extends AbstractArrayFactoryException
{
    private $value;

    private $discriminatorMap;

    /**
     * UnknownDiscriminatorValueException constructor.
     *
     * @param DocumentFactoryInterface $factory
     * @param string                   $value
     * @param array                    $discriminatorMap
     */
    public function __construct(DocumentFactoryInterface $factory, $value, array $discriminatorMap)
    {
        $this->value = $value;
        $this->discriminatorMap = $discriminatorMap;
        parent::__construct(
            $factory,
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