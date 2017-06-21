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

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Vainyl\Core\Exception\MissingRequiredServiceException;
use Vainyl\Core\Extension\AbstractExtension;
use Vainyl\Core\Extension\AbstractFrameworkExtension;

/**
 * Class DoctrineODMExtension
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class DoctrineODMExtension extends AbstractFrameworkExtension
{
    /**
     * @inheritDoc
     */
    public function load(array $configs, ContainerBuilder $container): AbstractExtension
    {
        parent::load($configs, $container);

        if (false === $container->hasDefinition('doctrine.configuration.odm')) {
            throw new MissingRequiredServiceException($container, 'doctrine.configuration.odm');
        }

        $configuration = new DoctrineODMConfiguration();
        $odmConfig = $this->processConfiguration($configuration, $configs);

        $definition = $container->getDefinition('doctrine.configuration.odm');
        $definition->replaceArgument(2, $odmConfig['database']);
        $definition->replaceArgument(3, $odmConfig['config']);
        $definition->replaceArgument(4, $odmConfig['file']);
        $definition->replaceArgument(5, $odmConfig['extension']);
        $definition->replaceArgument(6, $odmConfig['tmp_dir']);
        $definition->replaceArgument(7, $odmConfig['proxy']);
        $definition->replaceArgument(8, $odmConfig['hydrator']);

        return $this;
    }
}
