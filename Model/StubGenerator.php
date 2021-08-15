<?php

/**
 * @copyright EveryWorkflow. All rights reserved.
 */

declare(strict_types=1);

namespace EveryWorkflow\DeveloperBundle\Model;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\KernelInterface;

class StubGenerator implements StubGeneratorInterface
{
    public const TWIG_TEMPLATE_NAME = 'DeveloperBundle_Stub';

    protected string $projectRoot;

    protected $finder;
    protected Filesystem $filesystem;
    protected KernelInterface $kernel;

    public function __construct(
        Filesystem $filesystem,
        KernelInterface $kernel
    ) {
        $this->filesystem = $filesystem;
        $this->kernel = $kernel;
    }

    public function getFinder(): Finder
    {
        if ($this->finder) {
            return $this->finder;
        }
        $this->finder = new Finder();

        return $this->finder;
    }

    public function getProjectDir(): string
    {
        if (isset($this->projectRoot)) {
            return $this->projectRoot;
        }
        $this->projectRoot = $this->kernel->getProjectDir();

        return $this->projectRoot;
    }

    /**
     * Generates stub using twig render.
     *
     * @throws \Exception
     */
    public function generate(StubInterface $stub): string
    {
        $dumpFilePath = $this->getProjectDir() . $stub->getFileDir() . '/' . $stub->getFileName();
        $dumpFilePath .= $stub->getFileExtension() ?? '.php';

        if ($this->filesystem->exists($dumpFilePath)) {
            throw new \Exception($dumpFilePath . ' - already exists');
        }

        if (!$this->filesystem->exists($stub->getStubPath())) {
            throw new \Exception($stub->getStubPath() . ' - not found');
        }

        /* Getting stub content */
        $stubContent = file_get_contents($stub->getStubPath());

        /* Using twig for stub render */
        $loader = new \Twig\Loader\ArrayLoader([
            self::TWIG_TEMPLATE_NAME => $stubContent,
        ]);
        $twig = new \Twig\Environment($loader);
        $newContent = $twig->render(self::TWIG_TEMPLATE_NAME, $stub->toArray());

        /* Dumping generated file to $dumpFilePath */
        $this->filesystem->dumpFile($dumpFilePath, $newContent);

        return $dumpFilePath;
    }
}
