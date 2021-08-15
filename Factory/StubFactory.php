<?php

/**
 * @copyright EveryWorkflow. All rights reserved.
 */

declare(strict_types=1);

namespace EveryWorkflow\DeveloperBundle\Factory;

use EveryWorkflow\CoreBundle\Model\DataObject;
use EveryWorkflow\DeveloperBundle\Model\Stub;
use EveryWorkflow\DeveloperBundle\Model\StubInterface;

class StubFactory implements StubFactoryInterface
{
    protected string $appNamespace;
    protected string $appDir;
    protected string $generateFileHeader;

    public function __construct(string $appNamespace, string $appDir, string $generateFileHeader)
    {
        $this->appNamespace = $appNamespace;
        $this->appDir = $appDir;
        $this->generateFileHeader = $generateFileHeader;
    }

    public function create(?string $file, ?string $dir, ?string $bundle): StubInterface
    {
        $dataObj = new DataObject();

        /* Presetting config for stub */
        $dataObj->setData(StubInterface::KEY_GENERATE_FILE_HEADER, $this->generateFileHeader);

        $dataObj->setData(StubInterface::KEY_APP_NAMESPACE, $this->appNamespace);

        /* For bundle */
        $bundleNamespace = $this->appNamespace;
        if ($bundle) {
            $bundleNamespace .= '\\' . ucwords(str_replace('/', '\\', $bundle));
        }
        $dataObj->setData(StubInterface::KEY_BUNDLE_NAMESPACE, $bundleNamespace);

        /* For file */
        $filePieces = explode('/', $file);
        $fileName = array_pop($filePieces);
        $dataObj->setData(StubInterface::KEY_FILE_NAME, $fileName);

        $fileNamespace = $bundleNamespace;
        if ($dir) {
            $fileNamespace .= '\\' . ucwords(str_replace('/', '\\', $dir));
        }
        if (count($filePieces)) {
            $fileNamespace .= '\\' . implode('\\', $filePieces);
        }
        $dataObj->setData(StubInterface::KEY_FILE_NAMESPACE, $fileNamespace);

        $fileDir = '/' . $this->appDir . $bundle;
        if ($dir) {
            $fileDir .= '/' . $dir;
        }
        if (count($filePieces)) {
            $fileDir .= '/' . implode('/', $filePieces);
        }
        $dataObj->setData(StubInterface::KEY_FILE_DIR, $fileDir);

        return new Stub($dataObj);
    }
}
