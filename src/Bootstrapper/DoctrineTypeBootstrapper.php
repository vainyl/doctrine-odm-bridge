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

namespace Vainyl\Doctrine\ODM\Bootstrapper;

use Doctrine\ODM\MongoDB\Types\Type;
use Vainyl\Core\AbstractIdentifiable;
use Vainyl\Core\Application\ApplicationInterface;
use Vainyl\Core\Application\BootstrapperInterface;
use Vainyl\Doctrine\ODM\Type\TimeType;
use Vainyl\Time\Factory\TimeFactoryInterface;

/**
 * Class DoctrineTypeBootstrapper
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class DoctrineTypeBootstrapper extends AbstractIdentifiable implements BootstrapperInterface
{
    private $timeFactory;

    /**
     * DoctrineTypeBootstrapper constructor.
     *
     * @param TimeFactoryInterface $timeFactory
     */
    public function __construct(TimeFactoryInterface $timeFactory)
    {
        $this->timeFactory = $timeFactory;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'doctrine.odm_type';
    }

    /**
     * @inheritDoc
     */
    public function process(ApplicationInterface $application): BootstrapperInterface
    {
        Type::addType('v_time', TimeType::class);
        Type::getType('v_time')->setTimeFactory($this->timeFactory);

        return $this;
    }
}