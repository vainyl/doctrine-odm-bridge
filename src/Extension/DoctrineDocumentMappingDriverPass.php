<?php
/**
 * Vainyl
 *
 * PHP Version 7
 *
 * @package   Doctrine-odm-bridge
 * @license   https://opensource.org/licenses/MIT MIT License
 * @link      https://vainyl.com
 */
declare(strict_types=1);

namespace Vainyl\Doctrine\ODM\Extension;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Vainyl\Core\Exception\MissingRequiredServiceException;

/**
 * Class DoctrineDocumentMappingDriverPass
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class DoctrineDocumentMappingDriverPass implements CompilerPassInterface
{
    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container)
    {
        foreach ($container->getParameter('doctrine.decorators.document') as $decorator) {
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
    }
}