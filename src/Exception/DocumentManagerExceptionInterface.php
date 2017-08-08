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
use Vainyl\Core\ArrayInterface;

/**
 * Interface DocumentManagerExceptionInterface
 * @package Vainyl\Doctrine\ODM\Exception
 */
interface DocumentManagerExceptionInterface extends ArrayInterface, \Throwable
{
    /**
     * @return DocumentManager
     */
    public function getDocumentManager(): DocumentManager;
}