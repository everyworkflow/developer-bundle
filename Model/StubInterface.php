<?php

/**
 * @copyright EveryWorkflow. All rights reserved.
 */

declare(strict_types=1);

namespace EveryWorkflow\DeveloperBundle\Model;

use EveryWorkflow\CoreBundle\Model\DataObjectInterface;
use EveryWorkflow\CoreBundle\Support\ArrayableInterface;

interface StubInterface extends ArrayableInterface, DataObjectInterface
{
    public const KEY_STUB_PATH = 'stub_path';

    public const KEY_FILE_NAME = 'file_name';
    public const KEY_FILE_EXTENSION = 'file_extension';
    public const KEY_FILE_DIR = 'file_dir';
    public const KEY_FILE_NAMESPACE = 'file_namespace';
    public const KEY_BUNDLE_NAMESPACE = 'bundle_namespace';
    public const KEY_APP_NAMESPACE = 'app_namespace';

    public const KEY_GENERATE_FILE_HEADER = 'generate_file_header';

    public function setStubPath(string $stubPath): self;

    public function getStubPath(): ?string;

    public function setFileName(string $fileName): self;

    public function getFileName(): ?string;

    public function setFileExtension(string $fileExtension): self;

    public function getFileExtension(): ?string;

    public function setFileDir(string $fileDir): self;

    public function getFileDir(): ?string;

    public function setFileNamespace(string $fileNamespace): self;

    public function getFileNamespace(): ?string;

    public function setBundleNamespace(string $bundleNamespace): self;

    public function getBundleNamespace(): ?string;

    public function setGenerateFileHeader(string $makeAuthor): self;

    public function getGenerateFileHeader(): string;
}
