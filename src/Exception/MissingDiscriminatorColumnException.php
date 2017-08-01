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
 * Class MissingDiscriminatorColumnException
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class MissingDiscriminatorColumnException extends AbstractHydratorException
{
    private $column;

    private $externalData;

    /**
     * MissingDiscriminatorColumnException constructor.
     *
     * @param HydratorInterface $hydrator
     * @param string            $column
     * @param array             $externalData
     */
    public function __construct(HydratorInterface $hydrator, string $column, array $externalData)
    {
        $this->column = $column;
        $this->externalData = $externalData;
        parent::__construct(
            $hydrator,
            sprintf('Column %s not found in external data %s', $column, json_encode($externalData))
        );
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return array_merge(['column' => $this->column, 'external_data' => $this->externalData], parent::toArray());
    }
}