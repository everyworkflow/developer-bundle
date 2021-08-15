<?php

/**
 * @copyright EveryWorkflow. All rights reserved.
 */

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use EveryWorkflow\DeveloperBundle\Command\GenerateBundleCommand;
use EveryWorkflow\DeveloperBundle\Factory\StubFactory;
use Symfony\Component\VarDumper\Cloner\Stub;

return function (ContainerConfigurator $configurator) {
    $services = $configurator
        ->services()
        ->defaults()
        ->autowire()
        ->autoconfigure();

    $services
        ->load('EveryWorkflow\\DeveloperBundle\\', '../../*')
        ->exclude('../../{DependencyInjection,Resources,Tests}');

    $services->set(StubFactory::class)
        ->arg('$appDir', '%env(APP_DIR)%')
        ->arg('$appNamespace', '%env(APP_NAMESPACE)%')
        ->arg('$generateFileHeader', '%env(GENERATE_FILE_HEADER)%');

    $services->set(Stub::class)->factory([StubFactory::class, 'create']);

    $services->set(GenerateBundleCommand::class)
        ->arg('$appNamespace', '%env(APP_NAMESPACE)%');
};
