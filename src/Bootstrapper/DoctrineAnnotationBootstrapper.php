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

namespace Vainyl\Doctrine\ODM\Bootstrapper;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Vainyl\Core\AbstractIdentifiable;
use Vainyl\Core\Application\ApplicationInterface;
use Vainyl\Core\Application\BootstrapperInterface;

/**
 * Class DoctrineAnnotationBootstrapper
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class DoctrineAnnotationBootstrapper extends AbstractIdentifiable implements BootstrapperInterface
{
    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'doctrine.odm_annotation';
    }

    /**
     * @inheritDoc
     */
    public function process(ApplicationInterface $application): BootstrapperInterface
    {
        AnnotationRegistry::registerLoader('class_exists');

        return $this;
    }
}
