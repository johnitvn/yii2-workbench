<?php

namespace johnitvn\workbench;

use yii\helpers\FileHelper;
use \RecursiveDirectoryIterator as RDIterator;

/**
 * Wraper class all job relation with file
 * @author John Martin <john.itvn@gmail.com>
 * @since 1.0.0
 */
class FileSystem {

    public static function makeDirectory($path, $mode = 0775, $recursive = true) {
        return FileHelper::createDirectory($path, $mode, $recursive);
    }

    public static function put($filename, $data, $context = null) {
        return file_put_contents($filename, $data, $context);
    }

    public static function get($filename) {
        return file_get_contents($filename);
    }

    /**
     * Get all sub directory
     * @param string $path The base path want to find
     * @return array Array of path of sub directory founded
     */
    public static function getAllSubDirectory($path) {
        $rdi = new RDIterator($path, RDIterator::SKIP_DOTS);
        $subDirs = [];
        foreach ($rdi as $item) {
            if ($item->isDir()) {
                $subDirs[] = $rdi->getPathname();
            }
        }
        return $subDirs;
    }

}
