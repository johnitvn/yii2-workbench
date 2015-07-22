<?php

namespace johnitvn\workbench;

use johnitvn\jsonquery\JsonDocument;

/**
 * 
 * @author John Martin <john.itvn@gmail.com>
 * @since 1.0.0
 */
class Package {

    /**
     * The vendor name of the package.
     *
     * @var string
     */
    public $vendor;

    /**
     * The snake-cased version of the vendor.
     *
     * @var string
     */
    public $lowerVendor;

    /**
     * The name of the package.
     *
     * @var string
     */
    public $name;

    /**
     * The snake-cased version of the package.
     *
     * @var string
     */
    public $lowerName;

    /**
     * The name of the author.
     *
     * @var string
     */
    public $author;

    /**
     * The email address of the author.
     *
     * @var string
     */
    public $email;

    /**
     * The path of workbench workspace
     * @var string 
     */
    public $workingDir;
    private $composerJsonDocument = false;

    /**
     * Create a new package instance.
     *
     * @param  string  $vendor
     * @param  string  $name
     * @param  string  $author
     * @param  string  $email
     * @return void
     */
    public function __construct(Workbench $workbench, $vendor, $name) {
        $this->name = $name;
        $this->vendor = $vendor;
        $this->workingDir = $workbench->workingDir;
        $this->email = $workbench->email;
        $this->author = $workbench->author;
        $this->lowerName = static::snake_case($name, '-');
        $this->lowerVendor = static::snake_case($vendor, '-');
    }

    /**
     * Get the full package name.
     *
     * @return string
     */
    public function getFullName() {
        return $this->lowerVendor . '/' . $this->lowerName;
    }

    /**
     * Get the fullpath of package
     * @return string
     */
    public function getFullPath() {
        return $this->workingDir . '/' . $this->getFullName();
    }

    /**
     * Get composer json document of this package
     * @return JsonDocument|null The composer json document or null when can't read composer.json file
     */
    public function getComposerJsonDocument() {
        if ($this->composerJsonDocument === false) {
            $this->composerJsonDocument = new JsonDocument();
            if ($json = @file_get_contents($this->getFullPath() . '/composer.json')) {
                try {
                    $this->composerJsonDocument->loadData($json);
                } catch (Exception $ex) {
                    $this->composerJsonDocument = null;
                }
            } else {
                $this->composerJsonDocument = null;
            }
        }
        return $this->composerJsonDocument;
    }

    public function getReplacement() {
        return [
            '{{lower_vendor}}' => $this->lowerVendor,
            '{{lower_name}}' => $this->lowerName,
            '{{vendor}}' => $this->vendor,
            '{{name}}' => $this->name,
            '{{author}}' => $this->author,
            '{{email}}' => $this->email,
        ];
    }

    private static function snake_case($value, $delimiter = '_') {
        $replace = '$1' . $delimiter . '$2';
        return ctype_lower($value) ? $value : strtolower(preg_replace('/(.)([A-Z])/', $replace, $value));
    }

}
