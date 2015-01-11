<?php

namespace divbyzero\LoggerAwareBundle\Tests\DependencyInjection\Compiler;

use divbyzero\LoggerAwareBundle\DependencyInjection\Compiler\LoggerInjectionCompilerPass;
use divbyzero\LoggerAwareBundle\LoggerAwareInterface;
use divbyzero\LoggerAwareBundle\LoggerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Psr\Log\LoggerInterface;

class InterfaceStub implements LoggerAwareInterface
{
    private $logger;

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function getLogger()
    {
        return $this->logger;
    }
}

class TraitStub {
    use LoggerAwareTrait { getLogger as public; }
}

class LoggerInjectionCompilerPassTest extends \PHPUnit_Framework_TestCase
{
    protected function process(ContainerBuilder $container, $serviceId)
    {
        $pass = new LoggerInjectionCompilerPass();
        $pass->process($container);

        return $container->get($serviceId);
    }

    public function testServiceWithInterface1()
    {
        $container = new ContainerBuilder();
        $container->register('a')->setClass('divbyzero\LoggerAwareBundle\Tests\DependencyInjection\Compiler\InterfaceStub');

        $obj = $this->process($container, 'a');
        $this->assertNull($obj->getLogger());
    }

    public function testServiceWithInterface2()
    {
        $container = new ContainerBuilder();
        
        $mock = $this->getMock('Psr\Log\LoggerInterface');
        $container->register('logger')->setClass(get_class($mock));

        $container->register('a')->setClass('divbyzero\LoggerAwareBundle\Tests\DependencyInjection\Compiler\InterfaceStub');

        $obj = $this->process($container, 'a');
        $this->assertNotNull($obj->getLogger());
    }

    public function testServiceWithTrait1()
    {
        $container = new ContainerBuilder();
        
        $mock = $this->getMock('Psr\Log\LoggerInterface');
        $container->register('logger')->setClass(get_class($mock));
        
        $container->register('a')->setClass('divbyzero\LoggerAwareBundle\Tests\DependencyInjection\Compiler\TraitStub');

        $obj = $this->process($container, 'a');
        $this->assertNotNull($obj->getLogger());
    }
}
