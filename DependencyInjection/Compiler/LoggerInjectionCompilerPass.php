<?php
/**
 * This file is part of the LoggerAwareBundle.
 *
 * (c) Martin Jansen <martin@divbyzero.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace divbyzero\LoggerAwareBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

/**
 * Symfony compiler pass for injecting the logger service
 *
 * This compiler pass injects the logger service into all registered
 * services that either implement the PSR-3 LoggerAwareInterface or that use.
 * the PSR-3 LoggerAwareTrait.
 *
 * @author Martin Jansen <martin@divbyzero.net>
 */
class LoggerInjectionCompilerPass implements CompilerPassInterface
{
    private static $serviceIdentifier = 'logger';

    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(self::$serviceIdentifier)) {
            return;
        }

        $services = $container->getServiceIds();

        foreach ($services as $id) {
            try {
                $definition = $container->getDefinition($id);
            } catch (InvalidArgumentException $e) {
                continue;
            }

            $class = $definition->getClass();
            
            $aware = in_array('Psr\Log\LoggerAwareInterface', class_implements($class))
                || in_array('Psr\Log\LoggerAwareTrait', class_uses($class));

            if (!$aware) {
                continue;
            }

            $definition->addMethodCall(
                'setLogger',
                array(new Reference(self::$serviceIdentifier))
            );
        }
    }
}
