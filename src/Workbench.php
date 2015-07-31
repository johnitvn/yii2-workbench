<?php

namespace johnitvn\workbench;

use yii\base\Component;
use yii\base\Application;

/**
 * @author John Martin <john.itvn@gmail.com>
 * @since 1.0.0
 */
class Workbench extends Component {

    /**
     * @var string The workbench workspace directory.
     */
    public $workingDir;

    /**
     *
     * @var string The author name use for create package composer.json
     */
    public $author = "Author";

    /**
     *
     * @var string The email of author use for create package composer.json
     */
    public $email = "email@example.com";

    /**
     * @var array|null Set false for include all packages.
     */
    public $onlyIncludePackages;

    /**
     * Get workbench compoment
     * @param yii\base\Application $app
     * @return \johnitvn\workbench\Workbench|null return Workbench or null if working directory is not exist
     */
    public static function getInstance(Application $app) {
        if (!$app->has("workbench")) {
            $workbench = new Workbench();
        } else {
            $workbench = $app->get("workbench");
        }
        
        if($workbench->workingDir===null){
            $workbench->workingDir = dirname(dirname(dirname(__DIR__))) . '/workbench';
        }
        
        // If workbench workspace not exist. return null
        if (!file_exists($workbench->workingDir)) {
            return null;
        }

        return $workbench;
    }

}
