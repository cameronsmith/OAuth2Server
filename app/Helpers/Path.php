<?php namespace App\Helpers;

class Path
{
    /**
     * Get the application's path.
     *
     * @return string
     */
    public static function getAppPath() {
        return __DIR__ . '/../..';
    }

    /**
     * The the storage path.
     *
     * @return string
     */
    public static function getStoragePath() {
        return self::getAppPath() . '/storage';
    }
}