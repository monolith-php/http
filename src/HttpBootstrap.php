<?php namespace Monolith\Http;

use Monolith\ComponentBootstrapping\ComponentBootstrap;
use Monolith\DependencyInjection\Container;

final class HttpBootstrap implements ComponentBootstrap
{
    public function bind(Container $container): void
    {
        $container->singleton(Request::class, function(callable $r) {
            return Request::fromGlobals();
        });
    }

    public function init(Container $container): void
    {
    }
}