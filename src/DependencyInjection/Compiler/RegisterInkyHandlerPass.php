<?php

namespace SeHo\InkyMailer\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RegisterInkyHandlerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        // Pass requires EventDispatcherInterface
        if (!$container->hasDefinition('event_dispatcher') && !$container->hasAlias('event_dispatcher')) {
            return;
        }

        // Pass requires ParameterBagInterface
        if (!$container->hasDefinition('parameter_bag') && !$container->hasAlias('parameter_bag')) {
            return;
        }

        // Pass requires Mailer
        if (!$container->hasDefinition('mailer') && !$container->hasAlias('mailer')) {
            return;
        }

        $inkyHandlers = $container->findTaggedServiceIds('seho_inky_mailer.handler');

        foreach ($inkyHandlers as $id => $tags) {
            $definition = $container->getDefinition($id);
            $definition->addMethodCall('setDispatcher', [new Reference('event_dispatcher')]);
            $definition->addMethodCall('setParameterBag', [new Reference('parameter_bag')]);
            $definition->addMethodCall('setMailer', [new Reference('mailer')]);
        }
    }
}
