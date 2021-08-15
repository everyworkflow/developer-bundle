<?php

/**
 * @copyright EveryWorkflow. All rights reserved.
 */

declare(strict_types=1);

namespace EveryWorkflow\DeveloperBundle\Command;

use Doctrine\Inflector\InflectorFactory;
use EveryWorkflow\DeveloperBundle\Factory\StubFactoryInterface;
use EveryWorkflow\DeveloperBundle\Model\StubGeneratorInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GenerateBundleCommand extends Command
{
    public const KEY_FILE = 'file';
    public const KEY_BUNDLE = 'bundle';

    protected static $defaultName = 'generate:bundle';

    protected string $appNamespace;

    protected StubFactoryInterface $stubFactory;
    protected StubGeneratorInterface $stubGenerator;

    public function __construct(
        StubFactoryInterface $stubFactory,
        StubGeneratorInterface $stubGenerator,
        string $appNamespace = '',
        string $name = null
    ) {
        parent::__construct($name);
        $this->stubGenerator = $stubGenerator;
        $this->stubFactory = $stubFactory;
        $this->appNamespace = $appNamespace;
    }

    /**
     * @return void
     *
     * @throws \Exception
     */
    protected function configure()
    {
        $this->setDescription('Generates command class')
            ->setHelp('Eg: bin/console generate:bundle EveryWorkflow UserBundle' . PHP_EOL
                . 'Eg: bin/console generate:bundle EveryWorkflow UserBundle')
            ->addArgument(self::KEY_BUNDLE, InputArgument::REQUIRED, 'Bundle Name');
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

        /** @var string $bundleName Preparing stub for generation */
        $bundleName = $input->getArgument(self::KEY_BUNDLE);
        $bundleName = ucfirst($bundleName);
        if (!strpos($bundleName, 'Bundle')) {
            $bundleName .= 'Bundle';
        }

        $bundleGeneralName = str_replace('Bundle', '', $bundleName);
        $bundleAliasName = $bundleGeneralName;
        if ($this->appNamespace !== 'EveryWorkflow') {
            $bundleAliasName = $this->appNamespace . $bundleAliasName;
        }
        $inflector = InflectorFactory::create()->build();
        $packageName = str_replace('_', '-', strtolower($inflector->tableize($bundleName)));

        /* Generate AppNamespaceNameBundle.php */
        $bundleNameWithNamespace = $this->appNamespace . $bundleName;
        $stub = $this->stubFactory->create($bundleNameWithNamespace, '', $bundleName);
        $stub->setStubPath(__DIR__ . '/../Resources/stub/Generate/Bundle/SampleBundle.php.stub')
            ->setData('bundle_alias_name', $bundleAliasName);

        $filePath = $this->stubGenerator->generate($stub);
        $inputOutput->success('Successfully generated:- ' . $filePath);

        /* Generate composer.json */
        $stub = $this->stubFactory->create('composer', '', $bundleName);
        $stub->setStubPath(__DIR__ . '/../Resources/stub/Generate/Bundle/SampleComposer.json.stub')
            ->setData('bundle_name', $bundleName)
            ->setData('package_name', $packageName);
        $stub->setFileExtension('.json');

        $filePath = $this->stubGenerator->generate($stub);
        $inputOutput->success('Successfully generated:- ' . $filePath);

        /* Generate DependencyInjection */
        $stub = $this->stubFactory->create($bundleAliasName . 'Extension', 'DependencyInjection', $bundleName);
        $stub->setStubPath(__DIR__ . '/../Resources/stub/Generate/Bundle/SampleExtension.php.stub')
            ->setData('bundle_alias_name', $bundleAliasName);
        $filePath = $this->stubGenerator->generate($stub);
        $inputOutput->success('Successfully generated:- ' . $filePath);

        /* Generate Resources */
        $stub = $this->stubFactory->create('services', 'Resources/config', $bundleName);
        $stub->setStubPath(__DIR__ . '/../Resources/stub/Generate/Bundle/SampleServices.php.stub')
            ->setData('bundle_name', $bundleName);
        $filePath = $this->stubGenerator->generate($stub);
        $inputOutput->success('Successfully generated:- ' . $filePath);

        /* Generate Routes */
        $stub = $this->stubFactory->create('routes', 'Resources/config', $bundleName);
        $stub->setStubPath(__DIR__ . '/../Resources/stub/Generate/Bundle/SampleRoutes.yaml.stub')
            ->setData('bundle_name', $bundleName);
        $stub->setFileExtension('.yaml');
        $filePath = $this->stubGenerator->generate($stub);
        $inputOutput->success('Successfully generated:- ' . $filePath);

        return Command::SUCCESS;
    }
}
