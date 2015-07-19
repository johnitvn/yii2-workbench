<?php

namespace johnitvn\workbench;

use yii\helpers\FileHelper;

/**
 * @author John Martin <john.itvn@gmail.com>
 * @since 1.0.0
 */
class FileSystem {

    public static function isDirectory($filename) {
        return is_dir($filename);
    }

    public static function makeDirectory($path, $mode = 0775, $recursive = true) {
        return FileHelper::createDirectory($path, $mode, $recursive);
    }

    public static function copy($source, $dest, $context = null) {
        if (is_file($source)) {
            $this->makeDirectory(dirname($dest), 0777, true);
            return copy($source, $dest);
        }
        return false;
    }

    public static function put($filename, $data, $context = null) {
        return file_put_contents($filename, $data, $context);
    }

    public static function get($filename) {
        return file_get_contents($filename);
    }

}
