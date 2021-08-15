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

class GenerateClassInterfacePairCommand extends Command
{
    public const KEY_FILE = 'file';
    public const KEY_BUNDLE = 'bundle';

    protected static $defaultName = 'generate:class-interface-pair';

    protected StubFactoryInterface $stubFactory;
    protected StubGeneratorInterface $stubGenerator;

    public function __construct(
        StubFactoryInterface $stubFactory,
        StubGeneratorInterface $stubGenerator,
        string $name = null
    ) {
        parent::__construct($name);
        $this->stubFactory = $stubFactory;
        $this->stubGenerator = $stubGenerator;
    }

    /**
     * @return void
     *
     * @throws \Exception
     */
    protected function configure()
    {
        $this->setDescription('Generates class and interface pair')
            ->setHelp('Eg: bin/console generate:class-interface-pair Model/Tests TestBundle')
            ->addArgument(self::KEY_FILE, InputArgument::REQUIRED, 'Command file')
            ->addArgument(self::KEY_BUNDLE, InputArgument::REQUIRED, 'Bundle dir');
    }

    /**
     * @return int
     *
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $inputOutput = new SymfonyStyle($input, $output);

        $inputOutput->title('Generate Class and Interface Pair');

        /** @var string $fileName */
        $fileName = $input->getArgument(self::KEY_FILE);

        /** @var string $bundleName */
        $bundleName = $input->getArgument(self::KEY_BUNDLE);

        /* Preparing interfaceStub for generation */
        $interfaceStub = $this->stubFactory->create(
            $fileName . 'Interface',
            null,
            $bundleName
        );
        $interfaceStub->setStubPath(__DIR__ . '/../Resources/stub/Generate/ClassInterface/SampleInterface.php.stub');

        /* Preparing classStub for generation */
        $classStub = $this->stubFactory->create(
            $fileName,
            null,
            $bundleName
        );
        $classStub->setStubPath(__DIR__ . '/../Resources/stub/Generate/ClassInterface/Sample.php.stub')
            ->setData('interface_name', $interfaceStub->getFileName());

        $interfaceFilePath = $this->stubGenerator->generate($interfaceStub);
        $inputOutput->success('Successfully generated interface:- ' . $interfaceFilePath);
        $inputOutput->newLine();
        $classFilePath = $this->stubGenerator->generate($classStub);
        $inputOutput->success('Successfully generated class:- ' . $classFilePath);

        return Command::SUCCESS;
    }
}
