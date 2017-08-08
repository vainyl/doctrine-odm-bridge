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

namespace Vainyl\Doctrine\ODM\Extension;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
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

        if (false === $container->hasDefinition('doctrine.settings.odm')) {
            throw new MissingRequiredServiceException($container, 'doctrine.settings.odm');
        }

        $configuration = new DoctrineODMConfiguration();
        $odmConfig = $this->processConfiguration($configuration, $configs);
        $container
            ->findDefinition('doctrine.settings.odm')
            ->replaceArgument(1, $odmConfig['database'])
            ->replaceArgument(2, $odmConfig['config'])
            ->replaceArgument(3, $odmConfig['file'])
            ->replaceArgument(4, $odmConfig['extension'])
            ->replaceArgument(5, $odmConfig['tmp_dir'])
            ->replaceArgument(6, $odmConfig['proxy'])
            ->replaceArgument(7, $odmConfig['hydrator']);

        foreach ($odmConfig['decorators'] as $decorator) {
            $decoratorId = 'doctrine.mapping.driver.' . $decorator;
            if (false === $container->hasDefinition($decoratorId)) {
                throw new MissingRequiredServiceException($container, $decoratorId);
            }
            $definition = (clone $container->getDefinition($decoratorId))
                ->setDecoratedService('doctrine.mapping.driver.document')
                ->clearTag('driver.decorator')
                ->replaceArgument(0, new Reference($decoratorId . '.document.inner'));
            $container->setDefinition($decoratorId . '.document', $definition);
        }

        return $this;
    }
}
