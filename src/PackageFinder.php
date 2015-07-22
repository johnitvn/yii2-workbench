<?php

namespace johnitvn\workbench;

use johnitvn\workbench\Workbench;
use johnitvn\workbench\FileSystem;
use Composer\Autoload\ClassLoader;

/**
 *
 * @author John Martin <john.itvn@gmail.com>
 * @since 1.0.0
 */
class PackageFinder {

    private $workbench;
    private $packages;
    private $psr4s;

    /**
     * @param \johnitvn\workbench\Workbench $workbench
     */
    public function __construct(Workbench $workbench) {
        $this->workbench = $workbench;
    }

    private function isIncludePackage(Package $package) {
        $includes = $this->workbench->onlyIncludePackages;
        if ($includes !== null) {
            if (!in_array($package->getFullName(), $includes)) {
                return false;
            }
        }
        return true;
    }

    public function overidePsr4(ClassLoader $loader) {
        foreach ($this->psr4s as $key => $value) {            
            $loader->setPsr4($key, $value);
        }
    }

    /**
     * @return \johnitvn\workbench\Package[] The array of package have been founded
     */
    public function findAllPackage() {
        $this->packges = [];
        foreach (FileSystem::getAllSubDirectory($this->workbench->workingDir) as $vendorPath) {
            foreach (FileSystem::getAllSubDirectory($vendorPath) as $packagePath) {
                $vendorName = basename($vendorPath);
                $packageName = basename($packagePath);
                $package = new Package($this->workbench, $vendorName, $packageName);
                if ($this->isIncludePackage($package)) {
                    $composerJsonDocument = $package->getComposerJsonDocument();
                    $psr4s = $composerJsonDocument->getValue('/autoload/psr-4');
                    foreach ($psr4s as $key => $value) {
                        $this->psr4s[$key] = $package->getFullPath() . DIRECTORY_SEPARATOR . $value;
                    }
                    $this->packges[] = $package;
                }
            }
        }
        return $this->packges;
    }

}
