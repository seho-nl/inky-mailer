<?php

namespace SeHo\InkyMailer;

use SeHo\InkyMailer\DependencyInjection\Compiler\RegisterInkyHandlerPass;
use SeHo\InkyMailer\Handler\InkyMailHandler;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class SehoInkyMailerBundle extends AbstractBundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new RegisterInkyHandlerPass());
    }

    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
                ->arrayNode('subject')
                    ->children()
                        ->scalarNode('prefix')->end()
                        ->scalarNode('suffix')->end()
                    ->end()
                ->end() // subject
                ->arrayNode('address')
                    ->isRequired()
                    ->children()
                        ->scalarNode('email')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('name')->end()
                    ->end()
                ->end() // address
            ->end()
        ;
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/services.yaml');

        $builder->registerForAutoconfiguration(InkyMailHandler::class)->addTag('seho_inky_mailer.handler');

        $containerParameters = $container->parameters();

        $containerParameters->set('seho_inky_mailer.subject.prefix', $config['subject']['prefix'] ?? null);
        $containerParameters->set('seho_inky_mailer.subject.suffix', $config['subject']['suffix'] ?? null);

        $containerParameters->set('seho_inky_mailer.address.email', $config['address']['email'] ?? null);
        $containerParameters->set('seho_inky_mailer.address.name', $config['address']['name'] ?? null);

//        $container->parameters()->set('mailer.envelope.sender')
    }
}
