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
    private static $timeFactory;

    /**
     * @param TimeFactoryInterface $timeFactory
     */
    public static function setTimeFactory(TimeFactoryInterface $timeFactory)
    {
        self::$timeFactory = $timeFactory;
    }

    /**
     * @param array $value
     *
     * @return TimeInterface
     */
    public static function getDateTime(array $value): TimeInterface
    {
        $seconds = $value['timestamp']->sec;
        $microseconds = str_pad((string)$value['timestamp']->usec, 6, '0', STR_PAD_LEFT); // ensure microseconds

        $datetime = \DateTime::createFromFormat(
            'Y-m-d H:i:s.u',
            (new \DateTime())->setTimestamp($seconds)->format('Y-m-d H:i:s') . '.'
            . $microseconds
        );

        return self::$timeFactory->createFromString($datetime->format('Y-m-d H:i:s.u'), $value['time_zone']);
    }

    /**
     * @inheritDoc
     */
    public function convertToDatabaseValue($value)
    {
        if (null === $value) {
            return null;
        }

        if ($value instanceof TimeInterface) {
            return (object)[
                'timestamp' => new \MongoDate($value->format('U'), $value->format('u')),
                'time_zone' => $value->getTimezone()->getAbbreviation(),
            ];
        }

        throw new \InvalidArgumentException(sprintf('%s is not a properly formatted TIME type.', get_class($value)));
    }

    /**
     * @inheritDoc
     */
    public function convertToPHPValue($value)
    {
        if ($value === null || $value instanceof TimeInterface) {
            return $value;
        }

        return self::getDateTime($value);
    }

    /**
     * @inheritDoc
     */
    public function closureToPHP()
    {
        return 'if ($value === null) { 
                    $return = null; 
                } else { 
                    $return = \\' . get_class($this) . '::getDateTime($value);
                }';
    }
}