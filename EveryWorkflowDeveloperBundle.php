<?php

/**
 * @copyright EveryWorkflow. All rights reserved.
 */

declare(strict_types=1);

namespace EveryWorkflow\DeveloperBundle;

use EveryWorkflow\DeveloperBundle\DependencyInjection\DeveloperExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class EveryWorkflowDeveloperBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new DeveloperExtension();
    }
}
