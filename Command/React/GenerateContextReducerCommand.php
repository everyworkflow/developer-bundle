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

class GenerateContextReducerCommand extends Command
{
    public const KEY_FILE = 'file';
    public const KEY_BUNDLE = 'bundle';

    protected static $defaultName = 'generate:react:context-reducer';

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
        $this->setDescription('Generates react context reducer')
            ->setHelp('Eg: bin/console generate:react:context-reducer TestScope TestBundle')
            ->addArgument(self::KEY_FILE, InputArgument::REQUIRED, 'React context reducer file')
            ->addArgument(self::KEY_BUNDLE, InputArgument::REQUIRED, 'Bundle dir');
    }

    /**
     * @return int
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $inputOutput = new SymfonyStyle($input, $output);

        $inputOutput->title('Generate react component');

        $dirPrefix = '';
        /** @var string $fileName */
        $fileName = $input->getArgument(self::KEY_FILE);
        if (str_contains($fileName, '/')) {
            $fileNameArr = explode('/', $fileName);
            if (count($fileNameArr) > 1) {
                $fileName = array_pop($fileNameArr);
                $dirPrefix = implode('/', $fileNameArr);
            }
        }

        /** @var string $bundleName */
        $bundleName = $input->getArgument(self::KEY_BUNDLE);

        $this->generateStateInterface($fileName, $dirPrefix, $bundleName, $inputOutput);
        $this->generateStateActionInterface($fileName, $dirPrefix, $bundleName, $inputOutput);
        $this->generateState($fileName, $dirPrefix, $bundleName, $inputOutput);
        $this->generateReducer($fileName, $dirPrefix, $bundleName, $inputOutput);
        $this->generateRectContext($fileName, $dirPrefix, $bundleName, $inputOutput);

        return Command::SUCCESS;
    }

    protected function generateStateInterface(
        string $fileName,
        string $dirPrefix,
        string $bundleName,
        SymfonyStyle $inputOutput
    ): void {
        $modelStub = $this->stubFactory->create(
            $fileName . 'StateInterface',
            $dirPrefix ? 'Resources/assets/' . $dirPrefix . '/Model' : 'Resources/assets/Model',
            $bundleName
        );
        $modelStub->setStubPath(__DIR__ .
            '/../../Resources/stub/Generate/Resources/assets/ContextReducer/Model/SampleStateInterface.tsx.stub');
        $modelStub->setFileExtension('.tsx');

        $filePath = $this->stubGenerator->generate($modelStub);
        $inputOutput->success('Successfully generated:- ' . $filePath);
    }

    protected function generateStateActionInterface(
        string $fileName,
        string $dirPrefix,
        string $bundleName,
        SymfonyStyle $inputOutput
    ): void {
        $modelStub = $this->stubFactory->create(
            $fileName . 'StateActionInterface',
            $dirPrefix ? 'Resources/assets/' . $dirPrefix . '/Model' : 'Resources/assets/Model',
            $bundleName
        );
        $modelStub->setStubPath(__DIR__ .
            '/../../Resources/stub/Generate/Resources/assets/ContextReducer/Model/SampleStateActionInterface.tsx.stub');
        $modelStub->setFileExtension('.tsx');

        $filePath = $this->stubGenerator->generate($modelStub);
        $inputOutput->success('Successfully generated:- ' . $filePath);
    }

    protected function generateState(
        string $fileName,
        string $dirPrefix,
        string $bundleName,
        SymfonyStyle $inputOutput
    ): void {
        $stateStub = $this->stubFactory->create(
            $fileName . 'State',
            $dirPrefix ? 'Resources/assets/' . $dirPrefix . '/State' : 'Resources/assets/State',
            $bundleName
        );
        $stateInterfaceNamespace = '@';
        if ($stateStub->getBundleNamespace()) {
            $stateInterfaceNamespace .= str_replace('\\', '/', (string) $stateStub->getBundleNamespace()) . '/Model/' . $fileName . 'StateInterface';
        }
        $stateStub->setStubPath(__DIR__ .
            '/../../Resources/stub/Generate/Resources/assets/ContextReducer/State/SampleState.tsx.stub');
        $stateStub->setFileExtension('.tsx')
            ->setData('state_interface_name', $fileName . 'StateInterface')
            ->setData('state_interface_namespace', $stateInterfaceNamespace);

        $filePath = $this->stubGenerator->generate($stateStub);
        $inputOutput->success('Successfully generated:- ' . $filePath);
    }

    protected function generateReducer(
        string $fileName,
        string $dirPrefix,
        string $bundleName,
        SymfonyStyle $inputOutput
    ): void {
        $reducerStub = $this->stubFactory->create(
            $fileName . 'Reducer',
            $dirPrefix ? 'Resources/assets/' . $dirPrefix . '/Reducer' : 'Resources/assets/Reducer',
            $bundleName
        );
        $stateInterfaceNamespace = '@';
        if ($reducerStub->getBundleNamespace()) {
            $stateInterfaceNamespace .= str_replace('\\', '/', (string) $reducerStub->getBundleNamespace()) . '/Model/' . $fileName . 'StateInterface';
        }
        $actionInterfaceNamespace = '@';
        if ($reducerStub->getBundleNamespace()) {
            $actionInterfaceNamespace .= str_replace('\\', '/', (string) $reducerStub->getBundleNamespace()) . '/Model/' . $fileName . 'StateActionInterface';
        }
        $reducerStub->setStubPath(__DIR__ .
            '/../../Resources/stub/Generate/Resources/assets/ContextReducer/Reducer/SampleReducer.tsx.stub');
        $reducerStub->setFileExtension('.tsx')
            ->setData('state_interface_name', $fileName . 'StateInterface')
            ->setData('state_interface_namespace', $stateInterfaceNamespace)
            ->setData('action_interface_name', $fileName . 'StateActionInterface')
            ->setData('action_interface_namespace', $actionInterfaceNamespace);

        $filePath = $this->stubGenerator->generate($reducerStub);
        $inputOutput->success('Successfully generated:- ' . $filePath);
    }

    protected function generateRectContext(
        string $fileName,
        string $dirPrefix,
        string $bundleName,
        SymfonyStyle $inputOutput
    ): void {
        $contextStub = $this->stubFactory->create(
            $fileName . 'Context',
            'Resources/assets/' . $dirPrefix . 'Context',
            $bundleName
        );
        $stateInterfaceNamespace = '@';
        if ($contextStub->getBundleNamespace()) {
            $stateInterfaceNamespace .= str_replace('\\', '/', (string) $contextStub->getBundleNamespace()) . '/Model/' . $fileName . 'StateInterface';
        }
        $stateNamespace = '@';
        if ($contextStub->getBundleNamespace()) {
            $stateNamespace .= str_replace('\\', '/', (string) $contextStub->getBundleNamespace()) . '/State/' . $fileName . 'State';
        }
        $contextStub->setStubPath(__DIR__ .
            '/../../Resources/stub/Generate/Resources/assets/ContextReducer/Context/SampleContext.tsx.stub');
        $contextStub->setFileExtension('.tsx')
            ->setData('context_interface_name', $fileName . 'ContextInterface')
            ->setData('state_interface_name', $fileName . 'StateInterface')
            ->setData('state_interface_namespace', $stateInterfaceNamespace)
            ->setData('state_name', $fileName . 'State')
            ->setData('state_namespace', $stateNamespace);

        $filePath = $this->stubGenerator->generate($contextStub);
        $inputOutput->success('Successfully generated:- ' . $filePath);
    }
}
