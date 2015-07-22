<?php

namespace johnitvn\workbench;

use Yii;
use yii\base\Exception;

/**
 * @author John Martin <john.itvn@gmail.com>
 * @since 1.0.0
 */
class PackageCreator {

    /**
     * 
     * @param \johnitvn\workbench\Package $package
     * @throws Exception
     */
    public function create(Package $package) {
        //Create workspace if not exist
        if (!file_exists($package->workingDir)) {
            if (!FileSystem::makeDirectory($package->workingDir)) {
                throw new Exception('Can\'t create workbench directory: ' . $package->workingDir);
            }
        }

        if (file_exists($package->getFullPath())) {
            echo "Package already exist!\n";
            echo "Skip!!!\n";
            exit(0);
        }

        if (!FileSystem::makeDirectory($package->getFullPath() . '/src')) {
            throw new Exception('Cannot create directory: ' . $package->getFullPath() . '/src');
        }

        if (!FileSystem::makeDirectory($package->getFullPath() . '/test')) {
            throw new Exception('Cannot create directory: ' . $package->getFullPath() . '/test');
        }

        if (!FileSystem::put($package->getFullPath() . '/composer.json', $this->loadStub($package->getReplacement(), 'composer.json'))) {
            throw new Exception('Cannot create composer.json : ' . $package->getFullPath() . '/composer.json');
        }

        if (!FileSystem::put($package->getFullPath() . '/src/Module.php', $this->loadStub($package->getReplacement(), 'Module.php'))) {
            throw new Exception('Cannot create Module.php : ' . $package->getFullPath() . '/Module.php');
        }

        if (!FileSystem::put($package->getFullPath() . '/phpunit.xml', $this->loadStub($package->getReplacement(), 'phpunit.xml'))) {
            throw new Exception('Cannot create phpunit.php : ' . $package->getFullPath() . '/phpunit.php');
        }
    }

    private function loadStub($replacements, $stubName) {
        return str_replace(array_keys($replacements), $replacements, FileSystem::get(__DIR__ . '/stubs/' . $stubName . '.raw'));
    }

}
