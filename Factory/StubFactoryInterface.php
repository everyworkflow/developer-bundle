<?php

/**
 * @copyright EveryWorkflow. All rights reserved.
 */

declare(strict_types=1);

namespace EveryWorkflow\DeveloperBundle\Factory;

use EveryWorkflow\DeveloperBundle\Model\StubInterface;

interface StubFactoryInterface
{
    public function create(?string $file, ?string $dir, ?string $bundle): StubInterface;
}
