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

use Documents\Functional\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Vainyl\Core\Exception\MissingRequiredServiceException;
use Vainyl\Core\Extension\AbstractExtension;
use Vainyl\Core\Extension\AbstractFrameworkExtension;
use Vainyl\Doctrine\ODM\Factory\DoctrineODMSettings;

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
        $settingsDefinition = (new Definition())
            ->setClass(DoctrineODMSettings::class)
            ->setArguments(
                [
                    new Reference('doctrine.settings'),
                    $odmConfig['database'],
                    $odmConfig['config'],
                    $odmConfig['file'],
                    $odmConfig['extension'],
                    $odmConfig['tmp_dir'],
                    $odmConfig['proxy'],
                    $odmConfig['hydrator'],
                ]
            );
        $container->setDefinition('doctrine.settings.odm', $settingsDefinition);
        $definition = $container->getDefinition('doctrine.configuration.odm');
        $definition->replaceArgument(1, new Reference('doctrine.settings.odm'));

        return $this;
    }
}
