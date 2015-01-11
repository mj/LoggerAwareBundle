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
 * services that either implement LoggerAwareInterface or that use
 * the LoggerAwareTrait trait.
 *
 * @author Martin Jansen <martin@divbyzero.net>
 */
class LoggerInjectionCompilerPass implements CompilerPassInterface
{
    private static $serviceIdentifier = 'logger';
    private static $baseNS = 'divbyzero\LoggerAwareBundle';

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
            
            $aware = in_array(self::$baseNS . '\LoggerAwareInterface', class_implements($class))
                || in_array(self::$baseNS . '\LoggerAwareTrait', class_uses($class));

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
