<?php

namespace johnitvn\workbench;

use Yii;
use johnitvn\workbench\json\Document;
use yii\base\Exception;
use yii\base\BootstrapInterface;

/**
 *
 * @author John Martin <john.itvn@gmail.com>
 * @since 1.0.0
 */
class Starter {

    public function start($app) {
        if (!$app->has("workbench")) {
            $workbench = new Workbench();
        } else {
            $workbench = $app->get("workbench");
        }

        if (!file_exists($workbench->workbenchDir)) {
            return;
        }
        foreach (new \DirectoryIterator($workbench->workbenchDir) as $vendor) {
            if ($vendor->isDir() && $vendor->getFilename() !== '.' && $vendor->getFilename() !== '..') {
                foreach (new \DirectoryIterator($vendor->getPathname()) as $package) {
                    if ($package->isDir() && $package->getFilename() !== '.' && $package->getFilename() !== '..') {
                        Yii::trace("Found package:" . $vendor->getFilename() . '/' . $package->getFilename(), "johnitvn\workbench\Starter::start");
                        if ($this->isIncludePackage($workbench, $vendor, $package)) {
                            $this->bootPackage($app, $package->getPathname(), $vendor->getFilename() . '/' . $package->getFilename());
                        }
                    }
                }
            }
        }
    }

    private function isIncludePackage($workbench, $vendor, $package) {
        $fullname = $vendor->getFilename() . '/' . $package->getFilename();
        $includes = $workbench->onlyIncludePackages;


        if ($includes !== null) {
            if (!in_array($fullname, $includes)) {

                return false;
            }
        }
        return true;
    }

    public function bootPackage($app, $packagePath, $packageFullName) {
        Yii::trace('Workbench boot package: ' . $packageFullName, 'johnitvn\workbench\Starter::bootPackage');
        $document = new Document();
        if (!$json = @file_get_contents($packagePath . '/composer.json')) {
            //skip
            return;
        } else {
            try {
                $document->loadData($json);
            } catch (Exception $ex) {
                //skip
                return;
            }
        }
        $vendorDir = $document->getValue("/config/vendor-dir", 'vendor');
        require $packagePath . '/' . $vendorDir . '/autoload.php';

        $bootstrapClass = $document->getValue("/extra/bootstrap", null);
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
