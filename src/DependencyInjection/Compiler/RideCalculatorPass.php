<?php declare(strict_types=1);

namespace App\DependencyInjection\Compiler;

use App\RideCalculator\RideCalculatorManagerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class RideCalculatorPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(RideCalculatorManagerInterface::class)) {
            return;
        }

        $rideNamerList = $container->findDefinition(RideCalculatorManagerInterface::class);

        $taggedServices = $container->findTaggedServiceIds('ride_calculator');

        foreach ($taggedServices as $id => $tags) {
            $rideNamerList->addMethodCall('addRideCalculator', [new Reference($id)]);
        }
    }
}
