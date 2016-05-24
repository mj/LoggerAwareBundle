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

use divbyzero\LoggerAwareBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

/**
 * Symfony compiler pass for injecting the logger service
 *
 * This compiler pass injects the configured service into all registered
 * services that either implement the PSR-3 LoggerAwareInterface or that use
 * the PSR-3 LoggerAwareTrait.
 *
 * @author Martin Jansen <martin@divbyzero.net>
 */
class LoggerInjectionCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        // Resolve bundle configuration
        $config = $this->processConfiguration(
            $container->getExtensionConfig('logger_aware')
        );
        $loggerService = $config['logger_service'];

        if (!$container->has($loggerService)) {
            throw new InvalidConfigurationException(sprintf(
                '@%s configured to be injected as logger, ' .
                'but not present in container',
                $loggerService
            ));
        }

        $logger = $container->findDefinition($loggerService);

        // For compliance with the injected setLogger method call, the
        // configured logger service must implement LoggerInterface.
        if (!in_array(
            'Psr\Log\LoggerInterface', 
            class_implements($logger->getClass())
        )) {
            throw new InvalidConfigurationException(sprintf(
                '@%s (%s) configured to be injected as logger, ' .
                'but does not implement Psr\Log\LoggerInterface',
                $loggerService,
                $logger->getClass()
            ));
        }

        $services = $container->getServiceIds();

        foreach ($services as $id) {
            try {
                $definition = $container->getDefinition($id);
            } catch (InvalidArgumentException $e) {
                continue;
            }

            $class = $definition->getClass();

            if (!$class) {
                continue;
            }

            $aware = in_array('Psr\Log\LoggerAwareInterface', class_implements($class))
                || in_array('Psr\Log\LoggerAwareTrait', class_uses($class));

            if (!$aware) {
                continue;
            }

            $definition->addMethodCall(
                'setLogger',
                array(new Reference($loggerService))
            );
        }
    }

    private function processConfiguration(array $configs)
    {
        $processor = new Processor();
        $configuration = new Configuration();

        return $processor->processConfiguration($configuration, $configs);
    }
}
