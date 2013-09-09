<?php

/*
 * This file is part of the SncRedisBundle package.
 *
 * (c) Henrik Westphal <henrik.westphal@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Snc\RedisBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class MonologPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $serviceId = 'snc_redis.monolog.handler';
        if ($container->hasDefinition($serviceId)) {
            $handlerDefinition = $container->getDefinition($serviceId);
            $monologConfigs = $container->getExtensionConfig('monolog');
            foreach ($monologConfigs as $monologConfig) {
                foreach ($monologConfig['handlers'] as $handler) {
                    if (isset($handler['id']) && $serviceId === $handler['id']) {
                        if (isset($handler['level'])) {
                            $level = $handler['level'] = is_int($handler['level']) ? $handler['level'] : constant('Monolog\Logger::'.strtoupper($handler['level']));
                            $handlerDefinition->addArgument($level);
                        }
                        if (isset($handler['bubble'])) {
                            $handlerDefinition->addArgument($handler['bubble']);
                        }

                        return;
                    }
                }
            }
        }
    }
}
