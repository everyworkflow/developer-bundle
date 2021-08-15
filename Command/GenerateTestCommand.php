<?php

/**
 * @copyright EveryWorkflow. All rights reserved.
 */

declare(strict_types=1);

namespace EveryWorkflow\DeveloperBundle\Command;

use EveryWorkflow\DeveloperBundle\Factory\StubFactoryInterface;
use EveryWorkflow\DeveloperBundle\Model\StubGeneratorInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GenerateTestCommand extends Command
{
    public const KEY_FILE = 'file';
    public const KEY_BUNDLE = 'bundle';

    protected static $defaultName = 'generate:test';

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
        $this->setDescription('Generates test class')
            ->setHelp('Eg: bin/console generate:test ExampleTest ExampleBundle')
            ->addArgument(self::KEY_FILE, InputArgument::REQUIRED, 'Test file')
            ->addArgument(self::KEY_BUNDLE, InputArgument::REQUIRED, 'Bundle dir');
    }

    /**
     * @return int
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $inputOutput = new SymfonyStyle($input, $output);

        $inputOutput->title('Generate Test');

        /** @var string $fileName */
        $fileName = $input->getArgument(self::KEY_FILE);
        if (!strpos($fileName, 'Test')) {
            $fileName .= 'Test';
        }

        /** @var string $bundleName */
        $bundleName = $input->getArgument(self::KEY_BUNDLE);

        /* Preparing stub for generation */
        $stub = $this->stubFactory->create(
            $fileName,
            'Tests/Unit',
            $bundleName
        );
        $stub->setStubPath(__DIR__ . '/../Resources/stub/Generate/Test/Unit/SampleTest.php.stub');

        $filePath = $this->stubGenerator->generate($stub);
        $inputOutput->success('Successfully generated:- ' . $filePath);

        return Command::SUCCESS;
    }
}
