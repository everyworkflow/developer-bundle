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
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

class GenerateMongoDocumentCommand extends Command
{
    public const KEY_DOCUMENT_NAME = 'document_name';
    public const KEY_BUNDLE = 'bundle';

    protected static $defaultName = 'generate:mogo-document';

    protected StubFactoryInterface $stubFactory;
    protected StubGeneratorInterface $stubGenerator;

    protected array $fieldName = [];

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
        $this->setDescription('Generates mongo document & repository pair')
            ->setHelp('Eg: bin/console generate:mogo-document User UserBundle')
            ->addArgument(self::KEY_DOCUMENT_NAME, InputArgument::REQUIRED, 'Mongo document')
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

        $inputOutput->title('Generate mongo document & repository pair');

        $this->generateDocument($input, $inputOutput);
        $inputOutput->newLine();

        $this->generateRepository($input, $inputOutput);
        $inputOutput->newLine();

        return Command::SUCCESS;
    }

    /**
     * @return void
     */
    protected function getDocumentField(InputInterface $input, SymfonyStyle $output)
    {
        $helper = $this->getHelper('question');
        $question = new Question("<fg=green>Please enter fieldname ? </> \n", '');
        $fieldName = $helper->ask($input, $output, $question);

        if (!$fieldName) {
            return;
        }

        $fieldData['org_field_name'] = $fieldName;
        $fieldData['camel_case'] = $this->camelCase($fieldName);
        $fieldData['field_name'] = strtoupper($this->uncamelCase($fieldName));
        $fieldData['fun_name'] = ucfirst($this->camelCase($fieldName));
        $fieldData['uncamel_case'] = $this->uncamelCase($fieldName);
        $this->fieldName[] = $fieldData;

        $this->getDocumentField($input, $output);
    }

    protected function generateDocument(InputInterface $input, SymfonyStyle $inputOutput): void
    {
        $documentName = $input->getArgument(self::KEY_DOCUMENT_NAME);
        if (!strpos($documentName, 'Document')) {
            $documentName .= 'Document';
        }

        $this->getDocumentField($input, $inputOutput);

        /* Preparing interfaceStub for generation */
        $interfaceStub = $this->stubFactory->create(
            $documentName . 'Interface',
            'Document',
            $input->getArgument(self::KEY_BUNDLE)
        );

        $interfaceStub->setStubPath(__DIR__ . '/../Resources/stub/Generate/MongoDocument/SampleInterface.php.stub')
            ->setData('fields', $this->fieldName);
        $interfaceFilePath = $this->stubGenerator->generate($interfaceStub);
        $inputOutput->success('Successfully generated interface:- ' . $interfaceFilePath);
        $inputOutput->newLine();

        /* Preparing classStub for generation */
        $classStub = $this->stubFactory->create(
            $documentName,
            'Document',
            $input->getArgument(self::KEY_BUNDLE)
        );
        $classStub->setStubPath(__DIR__ . '/../Resources/stub/Generate/MongoDocument/Sample.php.stub')
            ->setData('fields', $this->fieldName)
            ->setData('interface_name', $interfaceStub->getFileName());
        $classFilePath = $this->stubGenerator->generate($classStub);
        $inputOutput->success('Successfully generated class:- ' . $classFilePath);
    }

    protected function generateRepository(InputInterface $input, SymfonyStyle $inputOutput): void
    {
        $documentRepositoryName = $input->getArgument(self::KEY_DOCUMENT_NAME) . 'Repository';
        $collectionName = $input->getArgument(self::KEY_DOCUMENT_NAME);
        $collectionName = preg_replace('/[A-Z]/', '_$0', $collectionName);
        $collectionName = strtolower($collectionName);
        $collectionName = ltrim($collectionName, '_');
        $collectionName .= '_collection';

        /* Preparing interfaceStub for generation */
        $interfaceStub = $this->stubFactory->create(
            $documentRepositoryName . 'Interface',
            'Repository',
            $input->getArgument(self::KEY_BUNDLE)
        );
        $interfaceStub->setStubPath(__DIR__ .
            '/../Resources/stub/Generate/MongoDocument/SampleRepositoryInterface.php.stub');
        $interfaceFilePath = $this->stubGenerator->generate($interfaceStub);
        $inputOutput->success('Successfully generated interface:- ' . $interfaceFilePath);
        $inputOutput->newLine();

        /* Preparing classStub for generation */
        $classStub = $this->stubFactory->create(
            $documentRepositoryName,
            'Repository',
            $input->getArgument(self::KEY_BUNDLE)
        );
        $classStub->setStubPath(__DIR__ . '/../Resources/stub/Generate/MongoDocument/SampleRepository.php.stub')
            ->setData('interface_name', $interfaceStub->getFileName())
            ->setData('collection_name', $collectionName);
        $classFilePath = $this->stubGenerator->generate($classStub);
        $inputOutput->success('Successfully generated class:- ' . $classFilePath);
    }

    public function camelCase($str): string
    {
        $i = ['-', '_'];
        $str = preg_replace('/([a-z])([A-Z])/', '\\1 \\2', $str);
        $str = preg_replace('@[^a-zA-Z0-9\-_ ]+@', '', $str);
        $str = str_replace($i, ' ', $str);
        $str = str_replace(' ', '', ucwords(strtolower($str)));
        $str = strtolower($str[0]) . substr($str, 1);

        return $str;
    }

    public function uncamelCase($str): string
    {
        $str = preg_replace('/([a-z])([A-Z])/', '\\1_\\2', $str);
        $str = strtolower($str);

        return str_replace(' ', '_', $str);
    }
}
