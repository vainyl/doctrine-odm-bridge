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
    public function getCompilerPasses(): array
    {
        return [[new DoctrineDocumentMappingDriverPass()]];
    }

    /**
     * @inheritDoc
     */
    public function load(array $configs, ContainerBuilder $container): AbstractExtension
    {
        parent::load($configs, $container);

        if (false === $container->hasDefinition('doctrine.settings.document')) {
            throw new MissingRequiredServiceException($container, 'doctrine.settings.document');
        }

        $configuration = new DoctrineODMConfiguration();
        $odmConfig = $this->processConfiguration($configuration, $configs);
        $container
            ->findDefinition('doctrine.settings.document')
            ->replaceArgument(1, $odmConfig['database'])
            ->replaceArgument(2, $odmConfig['file'])
            ->replaceArgument(3, $odmConfig['extension'])
            ->replaceArgument(4, $odmConfig['tmp_dir'])
            ->replaceArgument(5, $odmConfig['proxy'])
            ->replaceArgument(6, $odmConfig['hydrator']);

        $container->setParameter('doctrine.decorators.document', $odmConfig['decorators']);

        return $this;
    }
}
