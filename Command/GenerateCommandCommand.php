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

class GenerateCommandCommand extends Command
{
    public const KEY_FILE = 'file';
    public const KEY_BUNDLE = 'bundle';

    protected static $defaultName = 'generate:command';

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
     *
     * @throws \Exception
     */
    protected function configure()
    {
        $this->setDescription('Generates command class')
            ->setHelp('Eg: bin/console generate:command CreateUserCommand UserBundle' . PHP_EOL
                . 'Eg: bin/console generate:command Create/UserCommand UserBundle')
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

        $inputOutput->title('Generate Command');

        /** @var string $fileName */
        $fileName = $input->getArgument(self::KEY_FILE);
        if (!strpos($fileName, 'Command')) {
            $fileName .= 'Command';
        }

        /** @var string $bundleName */
        $bundleName = $input->getArgument(self::KEY_BUNDLE);

        /* Preparing stub for generation */
        $stub = $this->stubFactory->create(
            $fileName,
            'Command',
            $bundleName
        );
        $stub->setStubPath(__DIR__ . '/../Resources/stub/Generate/Command/SampleCommand.php.stub');

        $filePath = $this->stubGenerator->generate($stub);
        $inputOutput->success('Successfully generated:- ' . $filePath);

        return Command::SUCCESS;
    }
}
