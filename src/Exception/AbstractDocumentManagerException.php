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

use Doctrine\ODM\MongoDB\DocumentManager;
use Vainyl\Core\Exception\AbstractCoreException;

/**
 * Class AbstractDocumentManagerException
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
abstract class AbstractDocumentManagerException extends AbstractCoreException implements
    DocumentManagerExceptionInterface
{
    private $documentManager;

    /**
     * AbstractDocumentManagerException constructor.
     *
     * @param DocumentManager $documentManager
     * @param string          $message
     * @param int             $code
     * @param \Throwable|null $previous
     */
    public function __construct(
        DocumentManager $documentManager,
        string $message,
        int $code = 500,
        \Throwable $previous = null
    ) {
        $this->documentManager = $documentManager;
        parent::__construct($message, $code, $previous);
    }

    /**
     * @inheritDoc
     */
    public function getDocumentManager(): DocumentManager
    {
        return $this->documentManager;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return array_merge(['document_manager' => spl_object_hash($this->documentManager)], parent::toArray());
    }
}