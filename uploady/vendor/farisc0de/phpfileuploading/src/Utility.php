<?php

namespace Farisc0de\PhpFileUploading;

/**
 * Utility Class
 *
 * @version 1.5.3
 * @category File_Upload
 * @package PhpFileUploading
 * @author fariscode <farisksa79@gmail.com>
 * @license MIT
 * @link https://github.com/farisc0de/PhpFileUploading
 */
class Utility
{
    /**
     * Ensure correct value for big ints
     *
     * @param int $int
     * @return float
     */
    public function fixintOverflow($int)
    {
        if ($int < 0) {
            $int += 2.0 * (PHP_INT_MAX + 1);
        }

        return $int;
    }
    /**
     * Return any type of readable storage size to bytes
     *
     * Example: 7.2 MB to 7201450
     *
     * @param int $size
     *  The readable size that you want to convert to bytes
     * @return float
     *  Return the bytes size as float
     */
    public function sizeInBytes($size)
    {
        $unit = "B";
        $units = array("B" => 0, "K" => 1, "M" => 2, "G" => 3, "T" => 4);
        $matches = array();
        preg_match("/(?<size>[\d\.]+)\s*(?<unit>b|k|m|g|t)?/i", $size, $matches);
        if (array_key_exists("unit", $matches)) {
            $unit = strtoupper($matches["unit"]);
        }
        return (floatval($matches["size"]) * pow(1024, $units[$unit]));
    }
    /**
     * Function that format file bytes to a readable format
     *
     * Example: 7201450 to 7.2 MB
     *
     * @param int $bytes
     *  The file size that you want to convert in bytes
     * @param int $precision
     *  The precision of the convertion how many digits you want after the dot
     * @return string
     *  Return the bytes as readable format
     */
    public function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Convert any type of storage size to a different unit
     *
     *
     * @param int $size
     *  The file size that you want to convert in bytes
     * @param string $unit
     *  The unit of the file size
     * @return float
     *  Return the file size as float
     */
    public function unitConvert($size, $unit)
    {
        switch ($unit) {
            case "B":
                return $size;
            case "KB":
                return $size / 1024;
            case "MB":
                return $size / 1024 / 1024;
            case "GB":
                return $size / 1024 / 1024 / 1024;
            case "TB":
                return $size / 1024 / 1024 / 1024 / 1024;
            default:
                return $size;
        }
    }

    /**
     * Function that helps with input filter and sanitize
     *
     * @param string $value
     *  The value of the malicious string you want to sanitize
     * @return string
     *  Return the sanitized string
     */
    public function sanitize($value)
    {
        if (!is_null($value)) {
            $data = trim($value);
            $data = htmlspecialchars($data, ENT_QUOTES, "UTF-8");
            $data = strip_tags($data);
            return $data;
        }
    }
    /**
     * Fix file input array to make it easy to iterate through it
     *
     * @param array $file_post
     *  The unarranged files array to fix
     * @return array
     *  Return a fixed and arranged files array based on PHP standerds
     */
    public function fixArray($file_post)
    {
        $file_array = array();
        $file_count = count($file_post['name']);
        $file_keys = array_keys($file_post);

        for ($i = 0; $i < $file_count; $i++) {
            foreach ($file_keys as $key) {
                $file_array[$i][$key] = $file_post[$key][$i];
            }
        }

        return $file_array;
    }

    /**
     * Set php.ini settings using an array
     *
     * Example: setINI(["file_uploads"=>1])
     *
     * @param array $ini_settings
     *  An array the contains the ini settings variables and values
     * @return void
     */
    public function setINI($ini_settings)
    {
        $sttings = [];

        foreach ($ini_settings as $key => $value) {
            ini_set($key, $value);
            $sttings[] = $key . "=" . $value;
        }

        if (is_file("php.ini") == false) {
            touch('php.ini');
        }

        file_put_contents('php.ini', '[PHP]' . "\n" . implode("\n", $sttings));
    }

    /**
     * Create a callback function when needed after or before an operation
     *
     * @param callback $function
     *  A callback function to execute
     * @param mixed $args
     *  A single paramter or an array that contains multiple paramters
     * @return mixed
     *  Return the callback function output
     */
    public function callback($function, $args = [])
    {
        if (is_callable($function)) {
            return call_user_func_array($function, $args);
        }
    }

    /**
     * Function to potect a folder
     *
     * @param string $folder_name
     *  The folder name you want protect
     * @return void
     */
    public function protectFolder($folder_name)
    {
        if (!file_exists($folder_name . "/" . ".htaccess")) {
            $content = "Options -Indexes" . "\n";
            $content .= "<Files .htaccess>" . "\n";
            $content .= "Order allow,deny" . "\n";
            $content .= "Deny from all" . "\n";
            $content .= "</Files>";
            @file_put_contents($this->sanitize($folder_name) .
                "/" . ".htaccess", $content);
        }

        if (!file_exists($this->sanitize($folder_name) . "/" . "index.php")) {
            $content = "<?php http_response_code(403); ?>";
            @file_put_contents($this->sanitize($folder_name) .
                "/" . "index.php", $content);
        }
    }
}
