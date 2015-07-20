<?php

namespace johnitvn\workbench;

use yii\base\Component;

/**
 * @author John Martin <john.itvn@gmail.com>
 * @since 1.0.0
 */
class Workbench extends Component {

    /**
     * @var string The workbench workspace directory.
     */
    public $workbenchDir = __DIR__ . '/../../../../workbench';

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

}
