<?php

/**
 * @copyright EveryWorkflow. All rights reserved.
 */

declare(strict_types=1);

namespace EveryWorkflow\DeveloperBundle\Model;

use Symfony\Component\Finder\Finder;

interface StubGeneratorInterface
{
    public function getFinder(): Finder;

    public function getProjectDir(): string;

    /**
     * Generates stub using twig render.
     *
     * @throws \Exception
     */
    public function generate(StubInterface $stub): string;
}
