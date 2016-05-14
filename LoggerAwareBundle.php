<?php
/**
 * This file is part of the LoggerAwareBundle.
 *
 * (c) Martin Jansen <martin@divbyzero.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace divbyzero\LoggerAwareBundle;

use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use divbyzero\LoggerAwareBundle\DependencyInjection\Compiler\LoggerInjectionCompilerPass;

/**
 *
 * @author Martin Jansen <martin@divbyzero.net>
 */
class LoggerAwareBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(
            new LoggerInjectionCompilerPass(),
            PassConfig::TYPE_BEFORE_REMOVING
        );
    }
}
