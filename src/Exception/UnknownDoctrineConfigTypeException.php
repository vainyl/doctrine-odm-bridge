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

use Vainyl\Core\Exception\AbstractCoreException;
use Vainyl\Doctrine\ODM\Factory\DoctrineODMConfigurationFactory;

/**
 * Class UnknownDoctrineConfigTypeException
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class UnknownDoctrineConfigTypeException extends AbstractCoreException
{
    private $configurationFactory;

    private $driver;

    /**
     * UnknownDoctrineDriverTypeException constructor.
     *
     * @param DoctrineODMConfigurationFactory $configurationFactory
     * @param string                       $driver
     */
    public function __construct(DoctrineODMConfigurationFactory $configurationFactory, string $driver)
    {
        $this->configurationFactory = $configurationFactory;
        $this->driver = $driver;
        parent::__construct(sprintf('Cannot create doctrine config reader of unknown type %s', $driver));
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return array_merge(
            ['configuration_factory' => $this->configurationFactory->getId(), 'driver' => $this->driver],
            parent::toArray()
        );
    }
}