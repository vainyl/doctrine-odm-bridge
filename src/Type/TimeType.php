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


namespace Vainyl\Doctrine\ODM\Type;

use Doctrine\ODM\MongoDB\Types\Type;
use Vainyl\Time\Factory\TimeFactoryInterface;
use Vainyl\Time\TimeInterface;

/**
 * Class TimeType
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class TimeType extends Type
{
    /**
     * @var TimeFactoryInterface
     */
    private $timeFactory;

    /**
     * @param TimeFactoryInterface $timeFactory
     *
     * @return $this
     */
    public function setTimeFactory(TimeFactoryInterface $timeFactory)
    {
        $this->timeFactory = $timeFactory;

        return $this;
    }

    public function convertToDatabaseValue($value)
    {
        if (null === $value) {
            return null;
        }

        if ($value instanceof TimeInterface) {
            return $value->toDateTime();
        }

        throw new \InvalidArgumentException(sprintf('%s is not a properly formatted TIME type.', get_class($value)));
    }

    public function convertToPHPValue($value)
    {
        if ($value === null || $value instanceof TimeInterface) {
            return $value;
        }

        return $this->timeFactory->createFromString($value);
    }
}