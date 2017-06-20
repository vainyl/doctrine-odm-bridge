<?php
/**
 * Vainyl
 *
 * PHP Version 7
 *
 * @package   Doctrine-orm-bridge
 * @license   https://opensource.org/licenses/MIT MIT License
 * @link      https://vainyl.com
 */
declare(strict_types=1);

namespace Vainyl\Doctrine\ODM\Extension;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class DoctrineODMConfiguration
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class DoctrineODMConfiguration implements ConfigurationInterface
{
    /**
     * @inheritDoc
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('doctrine_odm');

        $rootNode
            ->children()
                ->scalarNode('database')->defaultValue('doctrine')->end()
                ->scalarNode('config')->defaultValue('yaml')->end()
                ->scalarNode('file')->defaultValue('entitymap')->end()
                ->scalarNode('extension')->defaultValue('.odm.yml')->end()
                ->scalarNode('tmp_dir')->defaultValue('doctrine')->end()
                ->scalarNode('proxy')->defaultValue('Proxy')->end()
                ->scalarNode('hydrator')->defaultValue('Hydrator')->end()
            ->end();

        return $treeBuilder;
    }
}