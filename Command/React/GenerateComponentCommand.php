<?php

/**
 * @copyright EveryWorkflow. All rights reserved.
 */

declare(strict_types=1);

namespace EveryWorkflow\DeveloperBundle\Command\React;

use EveryWorkflow\DeveloperBundle\Factory\StubFactoryInterface;
use EveryWorkflow\DeveloperBundle\Model\StubGeneratorInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GenerateComponentCommand extends Command
{
    public const KEY_FILE = 'file';
    public const KEY_BUNDLE = 'bundle';

    protected static $defaultName = 'generate:react:component';

    protected StubFactoryInterface $stubFactory;
    protected StubGeneratorInterface $stubGenerator;

    public function __construct(
        StubFactoryInterface $stubFactory,
        StubGeneratorInterface $stubGenerator,
        string $name = null
    ) {
        parent::__construct($name);
        $this->stubGenerator = $stubGenerator;
        $this->stubFactory = $stubFactory;
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setDescription('Generates react component')
            ->setHelp('Eg: bin/console generate:react:component TestComponent TestBundle' . PHP_EOL
                . 'Eg: bin/console generate:react:component Sidebar/HeaderComponent FrontendBundle')
            ->addArgument(self::KEY_FILE, InputArgument::REQUIRED, 'React component file')
            ->addArgument(self::KEY_BUNDLE, InputArgument::REQUIRED, 'Bundle dir');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $inputOutput = new SymfonyStyle($input, $output);

        $inputOutput->title('Generate react component');

        /** @var string $fileName */
        $fileName = $input->getArgument(self::KEY_FILE);
        if (!strpos($fileName, 'Component')) {
            $fileName .= 'Component';
        }

        /** @var string $bundleName */
        $bundleName = $input->getArgument(self::KEY_BUNDLE);

        /* Preparing stub for generation */
        $fnComponentStub = $this->stubFactory->create(
            $fileName,
            'Resources/assets/' . $fileName,
            $bundleName
        );
        $fnComponentStub->setStubPath(__DIR__ .
            '/../../Resources/stub/Generate/Resources/assets/Component/SampleComponent.tsx.stub');
        $fnComponentStub->setFileExtension('.tsx');

        /* Preparing stub for generation */
        $indexStub = $this->stubFactory->create(
            'index',
            'Resources/assets/' . $fileName,
            $bundleName
        );
        $indexStub->setStubPath(__DIR__ .
            '/../../Resources/stub/Generate/Resources/assets/SampleIndex.tsx.stub');
        $indexStub->setFileExtension('.tsx')
            ->setData('functionComponentName', $fnComponentStub->getFileName());

        $filePath = $this->stubGenerator->generate($fnComponentStub);
        $inputOutput->success('Successfully generated:- ' . $filePath);

        $filePath = $this->stubGenerator->generate($indexStub);
        $inputOutput->success('Successfully generated:- ' . $filePath);

        return Command::SUCCESS;
    }
}
