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
 * Class UnknownReferenceEntityException
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class UnknownReferenceEntityException extends AbstractHydratorException
{
    private $documentName;

    private $referenceId;

    /**
     * UnknownReferenceEntityException constructor.
     *
     * @param HydratorInterface $hydrator
     * @param string            $documentName
     * @param array             $referenceId
     */
    public function __construct(HydratorInterface $hydrator, string $documentName, array $referenceId)
    {
        $this->documentName = $documentName;
        $this->referenceId = $referenceId;
        parent::__construct(
            $hydrator,
            sprintf('Cannot find reference document %s by id %s', $documentName, json_encode($referenceId))
        );
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return array_merge(
            ['document' => $this->documentName, 'reference_id' => $this->referenceId],
            parent::toArray()
        );
    }
}