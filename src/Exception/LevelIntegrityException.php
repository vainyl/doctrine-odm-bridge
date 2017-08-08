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

/**
 * Class LevelIntegrityException
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class LevelIntegrityException extends AbstractDocumentManagerException
{
    private $level;

    /**
     * LevelIntegrityException constructor.
     *
     * @param DocumentManager $documentManager
     * @param int             $level
     */
    public function __construct(DocumentManager $documentManager, int $level)
    {
        $this->level = $level;
        parent::__construct($documentManager, sprintf('Level integrity check failed for level %d', $level));
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return array_merge(['level' => $this->level], parent::toArray());
    }
}