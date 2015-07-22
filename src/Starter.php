<?php

namespace johnitvn\workbench;

use Yii;
use yii\base\Application;
use yii\base\Exception;
use yii\base\BootstrapInterface;
use johnitvn\workbench\Workbench;
use johnitvn\jsonquery\JsonDocument;
use Composer\Autoload\ClassLoader;

/**
 *
 * @author John Martin <john.itvn@gmail.com>
 * @since 1.0.0
 */
class Starter {

    /**
     * @param yii\base\Application $app
     * @return void
     */
    public function start(Application $app) {

        // Skip if workbench working dir is not exist
        if (($workbench = Workbench::getInstance($app)) == null) {
            return;
        }

        $finder = new PackageFinder($workbench);
        $packages = $finder->findAllPackage();

        foreach ($packages as $package) {
            if ($package->getComposerJsonDocument() == null) {
                //error on load composer.json
                continue;
            }
            $classLoader = $this->autoloadPackage($package);
            $finder->overidePsr4($classLoader);
            $this->bootPackage($package, $app);
        }
    }

    /**
     * Required autoload file of package in workbench workspace
     * @param string $packagePath
     * @param JsonDocument $composerJsonDocument
     * @return ClassLoader
     */
    private function autoloadPackage(Package $package) {
        $vendorDir = $package->getComposerJsonDocument()->getValue("/config/vendor-dir", 'vendor');
        return require $package->getFullPath() . '/' . $vendorDir . '/autoload.php';
    }

    /**
     * 
     * @param JsonDocument $composerJsonDocument
     * @param Application $app
     * @throws Exception
     * @return void
     */
    private function bootPackage(Package $package, Application $app) {
        $bootstrapClass = $package->getComposerJsonDocument()->getValue("/extra/bootstrap", null);
        if ($bootstrapClass !== null) {
            $bootstrap = new $bootstrapClass;
            if (!class_exists($bootstrapClass)) {
                throw new Exception($bootstrapClass . " is not exist!");
            } else if (!($bootstrap instanceof BootstrapInterface)) {
                throw new Exception($bootstrapClass . " must implements yii\base\BootstrapInterface");
            } else {
                Yii::trace('Boostrap with ' . $bootstrapClass, 'yii\base\Application::bootstrap');
                $bootstrap->bootstrap($app);
            }
        }
    }

}
