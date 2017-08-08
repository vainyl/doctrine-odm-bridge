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
use Vainyl\Doctrine\ODM\Factory\DoctrineDocumentMappingDriverFactory;

/**
 * Class UnknownDoctrineConfigTypeException
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class UnknownDoctrineConfigTypeException extends AbstractCoreException
{
    private $driverFactory;

    private $driver;

    /**
     * UnknownDoctrineDriverTypeException constructor.
     *
     * @param DoctrineDocumentMappingDriverFactory $driverFactory
     * @param string                               $driver
     */
    public function __construct(DoctrineDocumentMappingDriverFactory $driverFactory, string $driver)
    {
        $this->driverFactory = $driverFactory;
        $this->driver = $driver;
        parent::__construct(sprintf('Cannot create doctrine config reader of unknown type %s', $driver));
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return array_merge(
            ['factory' => $this->driverFactory->getId(), 'driver' => $this->driver],
            parent::toArray()
        );
    }
}