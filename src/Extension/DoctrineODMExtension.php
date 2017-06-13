<?php
/**
 * Vainyl
 *
 * PHP Version 7
 *
 * @package   Doctrine
 * @license   https://opensource.org/licenses/MIT MIT License
 * @link      https://vainyl.com
 */
declare(strict_types=1);

namespace Vainyl\Doctrine\ODM\Extension;

use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Vainyl\Core\Application\EnvironmentInterface;
use Vainyl\Core\Exception\MissingRequiredServiceException;
use Vainyl\Core\Extension\AbstractExtension;

/**
 * Class DoctrineODMExtension
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class DoctrineODMExtension extends AbstractExtension
{
    /**
     * @inheritDoc
     */
    public function load(
        array $configs,
        ContainerBuilder $container,
        EnvironmentInterface $environment = null
    ): AbstractExtension {
        if (false === $container->hasDefinition('database.document')) {
            throw new MissingRequiredServiceException($container, 'database.document');
        }

        $definition = $container->getDefinition('database.document');
        if ($definition->isSynthetic()) {
            $container->set('database.document', new Alias('database.document.doctrine'));
        }

        if (false === $container->hasDefinition('document.operation.factory')) {
            throw new MissingRequiredServiceException($container, 'document.operation.factory');
        }

        $definition = $container->getDefinition('document.operation.factory.doctrine');
        if ($definition->isSynthetic()) {
            $container->set('document.operation.factory', new Alias('document.operation.factory.doctrine'));
        }

        return parent::load($configs, $container, $environment);
    }
}
