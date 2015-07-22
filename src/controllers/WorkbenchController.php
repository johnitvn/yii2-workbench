<?php

namespace johnitvn\workbench\controllers;

use Yii;
use yii\console\Controller;
use johnitvn\workbench\Package;
use johnitvn\workbench\Workbench;
use johnitvn\workbench\PackageCreator;
use johnitvn\composerruntime\ComposerProcess;

/**
 * Manager workbench package
 * @author John Martin <john.itvn@gmail.com>
 * @since 1.0.0
 */
class WorkbenchController extends Controller {

    /**
     * Create the new package into workbench's workspace
     * @param string $vendor Vendor of new package
     */
    public function actionCreate($vendor = null) {
        if ($vendor == null) {
            $vendorName = $this->prompt('Enter vendor name: ');
            $packageName = $this->prompt('Enter package name: ');
        } else {
            $parts = explode("/", $vendor);
            if (count($parts) != 2) {
                echo 'Not understand verdor ' . $vendor . "\n";
                $vendorName = $this->prompt('Enter vendor name: ');
                $packageName = $this->prompt('Enter package name: ');
            } else {
                $vendorName = $parts[0];
                $packageName = $parts[1];
            }
        }
        echo "Starting create package...\n";
        if (!Yii::$app->has("workbench")) {
            echo "Not config workbench component. Get default author and email\n";
            $workbench = new Workbench();
        } else {
            $workbench = Yii::$app->get("workbench");
        }

        $package = new Package($workbench, $vendorName, $packageName);
        //do create package with template
        (new PackageCreator())->create($package);

        $process = new ComposerProcess($package->getFullPath());
        $process->run('install');
        $process->run('dump-autoload');
        echo "Done!!!\n";
    }

}
