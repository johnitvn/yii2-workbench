<?php

namespace johnitvn\workbench;

use Yii;
use yii\base\Exception;

/**
 * @author John Martin <john.itvn@gmail.com>
 * @since 1.0.0
 */
class PackageCreator {

    private $workbenchDir;

    /**
     * @param string $workbenchDir The workbench workspace directory
     * @throws Exception
     */
    public function __construct($workbenchDir) {
        $this->workbenchDir = $workbenchDir;
        //Create workspace if not exist
        if (!file_exists($this->workbenchDir)) {
            if (!FileSystem::makeDirectory($this->workbenchDir)) {
                throw new Exception('Can\'t create workbench directory: ' . $this->workbenchDir);
            }
        }
    }

    /**
     * 
     * @param \johnitvn\workbench\Package $package
     * @throws Exception
     * @return string The package path
     */
    public function create(Package $package) {

        $packagePath = $this->workbenchDir . "/" . $package->getFullName();

        if (file_exists($packagePath)) {
            echo "Package already exist!\n";
            echo "Skip!!!\n";
            exit(0);
        }

        if (!FileSystem::makeDirectory($packagePath . '/src')) {
            throw new Exception('Cannot create directory: ' . $packagePath . '/src');
        }

        if (!FileSystem::makeDirectory($packagePath . '/test')) {
            throw new Exception('Cannot create directory: ' . $packagePath . '/test');
        }

        if (!FileSystem::put($packagePath . '/composer.json', $this->loadStub($package->getReplacement(), 'composer.json'))) {
            throw new Exception('Cannot create composer.json : ' . $packagePath . '/composer.json');
        }

        if (!FileSystem::put($packagePath . '/src/Module.php', $this->loadStub($package->getReplacement(), 'Module.php'))) {
            throw new Exception('Cannot create Module.php : ' . $packagePath . '/Module.php');
        }

        if (!FileSystem::put($packagePath . '/phpunit.xml', $this->loadStub($package->getReplacement(), 'phpunit.xml'))) {
            throw new Exception('Cannot create phpunit.php : ' . $packagePath . '/phpunit.php');
        }
        return $packagePath;
    }

    private function loadStub($replacements, $stubName) {
        return str_replace(array_keys($replacements), $replacements, FileSystem::get(__DIR__ . '/stubs/' . $stubName . '.raw'));
    }

}
