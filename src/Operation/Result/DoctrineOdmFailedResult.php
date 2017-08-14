<?php
/**
 * Mmjitsu
 *
 * PHP Version 7.1
 *
 * @package   Api
 * @link      https://mmjitsu.com
 */
declare(strict_types=1);

namespace Vainyl\Doctrine\ODM\Operation\Result;

use Vainyl\Core\AbstractFailedResult;

/**
 * Class DoctrineOdmFailedResult
 *
 * @author  Andrey Dembitskiy <andrew.dembitskiy@gmail.com>
 *
 * @package Vainyl\Doctrine\ODM\Operation\Result
 */
class DoctrineOdmFailedResult extends AbstractFailedResult
{
    private $exception;

    /**
     * DoctrineOdmFailedResult constructor.
     *
     * @param \Throwable $exception
     */
    public function __construct(\Throwable $exception)
    {
        $this->exception = $exception;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return array_merge(
            [
                'code'    => $this->exception->getCode(),
                'message' => $this->exception->getMessage(),
                'file'    => $this->exception->getFile(),
                'line'    => $this->exception->getLine(),
                'trace'   => $this->exception->getTraceAsString(),
            ],
            parent::toArray()
        );
    }
}
