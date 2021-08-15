<?php

/**
 * @copyright EveryWorkflow. All rights reserved.
 */

declare(strict_types=1);

namespace EveryWorkflow\DeveloperBundle\Command;

use Carbon\Carbon;
use EveryWorkflow\DeveloperBundle\Factory\StubFactoryInterface;
use EveryWorkflow\DeveloperBundle\Model\StubGeneratorInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GenerateMigrationCommand extends Command
{
    public const KEY_FILE = 'file';
    public const KEY_BUNDLE = 'bundle';

    protected static $defaultName = 'generate:migration';

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
        $this->setDescription('Generates migration class')
            ->setHelp('Eg: bin/console generate:migration UserDataMigration UserBundle')
            ->addArgument(self::KEY_FILE, InputArgument::REQUIRED, 'Migration file')
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

        $inputOutput->title('Generate Migration');

        /** @var string $fileName */
        $fileName = $input->getArgument(self::KEY_FILE);
        $fileName = preg_replace('/[A-Z]/', '_$0', $fileName);
        $fileName = ltrim($fileName, '_');
        if (!strpos($fileName, 'Migration')) {
            $fileName .= '_Migration';
        }
        $fileName = 'Mongo_' . Carbon::now()->format('Y_m_d_H_i_s') . '_' . $fileName;

        /** @var string $bundleName */
        $bundleName = $input->getArgument(self::KEY_BUNDLE);

        /* Preparing interfaceStub for generation */
        $stub = $this->stubFactory->create(
            $fileName,
            'Migration',
            $bundleName
        );
        $stub->setStubPath(__DIR__ . '/../Resources/stub/Generate/Migration/SampleMigration.php.stub');

        $interfaceFilePath = $this->stubGenerator->generate($stub);
        $inputOutput->success('Successfully generated migration:- ' . $interfaceFilePath);
        $inputOutput->newLine(2);

        return Command::SUCCESS;
    }
}
