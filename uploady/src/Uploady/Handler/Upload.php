<?php

namespace Uploady\Handler;

/**
 * PHP Library to help you build your own file sharing website.
 *
 * @version 1.5.3
 * @category File_Upload
 * @package Uploady
 * @author fariscode <farisksa79@gmail.com>
 * @license MIT
 * @link https://github.com/farisc0de/Uploady
 */
final class Upload
{
    /**
     * HTML File Input
     * 
     * Example: $_FILES['file']
     *
     * @var array
     */
    private $upload_input;
    /**
     * Array For Filename Protection Filter
     *
     * @var array
     */
    private $name_array = [];
    /**
     * Array For File Protection Filter
     *
     * @var array
     */
    private $filter_array = [];
    /**
     * Class Controller Path
     *
     * @var string
     */
    private $controller;
    /**
     * Array For the upload folder data
     * 
     * Example: ["folder_name" => "upload", "folder_path" => "upload/"]
     *
     * @var array
     */
    private $upload_folder = [];
    /**
     * Size limit for protection
     *
     * @var int
     */
    private $size;
    /**
     * Image minimum width
     *
     * @var int
     */
    private $hash_name;
    /**
     * Image minimum width
     *
     * @var int
     */
    private $hash_id;
    /**
     * File ID for the database
     *
     * @var string
     */
    private $file_id;
    /**
     * User ID for the database
     *
     * @var string
     */
    private $user_id;
    /**
     * System Logs Array
     *
     * @var array
     */
    private $logs = [];
    /**
     * Array for the all uploaded files informations
     *
     * @var array
     */
    private $files = [];
    /**
     * Image miximum height
     *
     * @var int
     */
    private $max_height;
    /**
     * Image miximum width
     *
     * @var int
     */
    private $max_width;
    /**
     * Image minimum height
     *
     * @var int
     */
    private $min_height;
    /**
     * Image minimum width
     *
     * @var int
     */
    private $min_width;
    /**
     * Array list of error codes and the messages
     *
     * @var array
     */
    private $message = [
        0 => "File has been uploaded.",
        1 => "Invalid file format.",
        2 => "Failed to get MIME type.",
        3 => "File is forbidden.",
        4 => "Exceeded filesize limit.",
        5 => "Please select a file",
        6 => "File already exist.",
        7 => "Failed to move uploaded file.",
        8 => "The uploaded file's height is too large.",
        9 => "The uploaded file's width is too large.",
        10 => "The uploaded file's height is too small.",
        11 => "The uploaded file's width is too small.",
        12 => "The uploaded file's is too small.",
        13 => "The uploaded file is not a valid image.",
        14 => "Opreation does not exist.",
    ];

    /**
     * Class Constructor to initialize attributes
     *
     * @param array $upload_input
     *  An array of the upload file information coming from $_FILES
     * @param array $upload_folder
     *  An array that contain the upload folder full path and name
     * @param string $controller
     *  The folder name of folder that contains the json filters and the class file
     * @param string $upload_controller
     *  The upload controller that contains the factory code like upload.php
     * @param int $size
     *  The miximum size that the class allow to upload
     * @param int $max_height
     *  The miximum image height allowed
     * @param int $max_width
     *  The miximum image width allowed
     * @param int $min_height
     *  The minimum image height allowed
     * @param int $min_width
     *  The minimum image width allowed
     * @param string $file_id
     *  A uniqge if for the file to validate that the file exist
     * @param string $user_id
     *  A uniqge if for the user to validate the file owner
     * @return void
     */
    public function __construct(
        $upload_input = null,
        $upload_folder = [],
        $controller = null,
        $upload_controller = "upload.php",
        $size = "5 GB",
        $max_height = null,
        $max_width = null,
        $min_height = null,
        $min_width = null,
        $file_id = null,
        $user_id = null
    ) {
        $this->upload_input = $upload_input;
        $this->upload_folder = $upload_folder;
        $this->controller = $this->sanitize($controller);
        $this->upload_controller = $this->sanitize($upload_controller);
        $this->size = $this->sizeInBytes($this->sanitize($size));
        $this->max_height = $max_height;
        $this->max_width = $max_width;
        $this->min_height = $min_height;
        $this->min_width = $min_width;
        $this->file_id = $file_id;
        $this->user_id = $user_id;
    }
    /** 
     * Function to set upload input when needed
     *
     * @param array $upload_input
     *  An array of the upload file information coming from $_FILES
     * @return void
     */
    public function setUpload($upload_input)
    {
        $this->upload_input = $upload_input;
    }
    /**
     * Function to set the controller when needed
     * 
     * @param string $controller
     *  The folder name of folder that contains the json filters and the class file
     * @return void
     */
    public function setController($controller)
    {
        $this->controller = $this->sanitize($controller);
    }
    /**
     * Set the upload controller file
     * 
     * @param string $upload_controller
     *  The upload controller that contains the factory code like upload.php
     * @return void
     */
    public function setUploadController($upload_controller)
    {
        $this->upload_controller = realpath($this->sanitize($upload_controller));
    }
    /** 
     * Enable File Uploading Protection and Filters
     * 
     * @return void
     */
    public function enableProtection()
    {
        $this->name_array = json_decode(
            file_get_contents(
                $this->sanitize(
                    $this->controller . "forbidden.json"
                )
            )
        );

        $this->filter_array = json_decode(
            file_get_contents(
                $this->controller . "filter.json"
            ),
            true
        );
    }
    /**
     * Set Forbidden array to a custom list when needed
     *
     * @param array $forbidden_array
     *  An array that contains the forbidden file names like php shell names
     *  
     *  Example: ["aaa.php", "file.exe"]
     *  
     * @return void
     */
    public function setForbiddenFilter($forbidden_array)
    {
        $this->name_array = $forbidden_array; // Custom name filter array
    }
    /**
     * Set Extension array to a custom list when needed
     * 
     * @param array $filter_array
     *  An array that contains the allowed file extensions
     * @return void
     */
    public function setProtectionFilter($filter_array)
    {
        $this->filter_array = $filter_array;
    }
    /**
     * Set file size limit when needed
     * 
     * @param int $size
     *  The size you want to limit for each uploaded file
     * @return void
     */
    public function setSizeLimit($size)
    {
        $this->size = $this->fixintOverflow(
            $this->sizeInBytes(
                $this->sanitize(
                    $size
                )
            )
        );
    }
    /**
     * Set upload folder when needed
     *
     * @param array $folder_name
     *  An array contains the upload folder information full path and name
     * 
     *  Example: ["folder_name" => "upload", "folder_path" => realpath("upload")]
     * 
     * @return void
     */
    public function setUploadFolder($folder_name)
    {
        $this->upload_folder = $folder_name;
    }
    /**
     * Create a folder for a spesfic user
     *
     * @return true
     *  Returns true if the folder is created
     */
    public function createUserCloud($main_upload_folder)
    {
        $user_id = $this->getUserID();
        $user_cloud = $main_upload_folder .
            DIRECTORY_SEPARATOR .
            $user_id;

        if (!file_exists($user_cloud)) {
            @mkdir($user_cloud);
            @chmod($user_cloud, 777);
        }

        return true;
    }

    public function getUserCloud($main_upload_folder)
    {
        $user_id = $this->getUserID();
        $user_cloud = $main_upload_folder .
            DIRECTORY_SEPARATOR .
            $user_id;

        return $user_cloud;
    }
    /**
     * Firewall 1: Check File Extension
     *
     * @return bool
     *  Return true it the uploaded file extenstion is allowed
     */
    public function checkExtension()
    {
        if (key_exists(str_replace('.', '', $this->getExtension()), $this->filter_array)) {
            return true;
        } else {
            $this->addLog(['filename' => $this->hash_name, "message" => 1]);
            return false;
        }
    }
    /**
     * Function to return the file input extension
     * 
     * @return string
     *  Return the uploaded file extenstion as string
     */
    public function getExtension()
    {
        return strtolower(pathinfo($this->getName(), PATHINFO_EXTENSION));
    }
    /**
     * Firewall 2: Check File MIME Type
     * 
     * @return bool
     *  Return true if the uploaded file MIME type is allowed
     */
    public function checkMime()
    {
        $mime = mime_content_type($this->getTempName());

        if ($this->filter_array[$this->getExtension()] == $mime) {
            if ($mime == $this->getMime()) {
                return true;
            } else {
                $this->addLog(['filename' => $this->hash_name, "message" => 1]);
                return false;
            }
        }
    }
    /**
     * Function to get the MIME type using the server
     *
     * @return string
     *  Return the file MIME type as string
     */
    private function getMime()
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mtype = finfo_file($finfo, $this->getTempName());

        if (finfo_close($finfo)) {
            return $mtype;
        } else {
            $this->addLog(['filename' => $this->hash_name, "message" => 2]);
            return "application/octet-stream";
        }
    }
    /**
     * Function that return the uploaded file MIME type
     *
     * @return string
     *  Return the file MIME type as string
     */
    public function getFileType()
    {
        return $this->upload_input['type'];
    }
    /**
     * Firewall 3: Check File Name is Forbidden
     * 
     * @return bool
     *  Return true if the name is forbidden
     */
    public function checkForbidden()
    {
        if (!(in_array($this->hash_name, $this->name_array))) {
            return true;
        } else {
            $this->addLog(['filename' => $this->hash_name, "message" => 3]);
            return false;
        }
    }
    /**
     * Firewall 4: Check file size limit
     * 
     * @return bool
     *  Return true if the uploaded file size does not exceed the limit
     */
    public function checkSize()
    {
        if ($this->getSize() <= $this->size) {
            return true;
        } else {
            $this->addLog(['filename' => $this->hash_name, "message" => 4]);
            return false;
        }
    }
    /**
     * Return the size of the uploaded file as bytes
     * 
     * @return float
     *  Return the uploaded file size as bytes
     */
    public function getSize()
    {
        return $this->fixintOverflow($this->upload_input['size']);
    }
    /**
     * Function to check if the HTML input is empty
     * 
     * @return bool
     *  Return true if the the input contain a file false otherwise
     */
    public function checkIfEmpty()
    {
        if ($this->upload_input['error'] == UPLOAD_ERR_NO_FILE) {
            $this->addLog(['filename' => $this->hash_name, "message" => 5]);
            return false;
        } else {
            return true;
        }
    }
    /**
     * Return the name of the uploaded file
     * 
     * @return string
     *  Return the name of the uploaded file as string
     */
    public function getName()
    {
        return $this->upload_input['name'];
    }
    /**
     * Return the PHP Generated name for the uploaded file
     * 
     * @return string
     *  return the temp name that PHP generated for uploaded file
     */
    public function getTempName()
    {
        return $this->upload_input['tmp_name']; // Return the PHP Generated Temp name
    }
    /**
     * Generate a Qr Code of the download url
     *
     * @param string $qr_size
     *  The size ot the qr code image
     * @return string
     *  Return the qr code image url to display
     */
    public function generateQrCode($qr_size = "150x150")
    {
        return "https://chart.googleapis.com/chart?chs=" . $this->sanitize($qr_size) . "&cht=qr&chl=" . $this->generateDownloadLink() . "&choe=UTF-8";
    }
    /**
     * Generate a download link
     * 
     * @return string
     *  Return a well formatted download link with a custom download page
     */
    public function generateDownloadLink()
    {
        $file_id = $this->file_id;

        return sprintf(
            "%s/%s?file_id=%s",
            SITE_URL,
            "download.php",
            $file_id
        );
    }
    /**
     * Generate a delete link
     * 
     * @return string
     *  Return a well formatted delete file link with a custom delete page
     */
    public function generateDeleteLink()
    {
        // Get user paramters [ file_id, user_id ]
        $file_id = $this->file_id;
        $user_id = $this->user_id;

        return sprintf(
            "%s/%s?file_id=%s&user_id=%s",
            SITE_URL,
            "delete.php",
            $file_id,
            $user_id
        );
    }
    /**
     * Generate a direct download link
     * 
     * @return string
     *  Return a well formatted direct download link without a custom download page
     */
    public function generateDirectDownloadLink()
    {
        $filename = ($this->hash_name);

        return sprintf(
            "%s/%s/%s",
            SITE_URL,
            $this->upload_folder['folder_name'],
            $filename
        );
    }
    /**
     * Get the unique file id
     * 
     * @return string
     *  Return the uploaded file uniqe id
     */
    public function getFileID()
    {
        return $this->file_id;
    }
    /**
     * Set the unique file id manualy when needed
     * 
     * @param string $file_id
     *  The unique file id that you want
     * @return void
     */
    public function setFileID($file_id = "")
    {

        $this->file_id = $file_id;
    }
    /**
     * Get the unique user id for security
     * 
     * @return string
     *  Return the unique user id
     */

    public function getUserID()
    {
        return $this->user_id;
    }
    /**
     * Set the unique user id manualy when needed
     * 
     * @param string $user_id
     *  The unique file id that you want
     * @return void
     */
    public function setUserID($user_id = "")
    {

        $this->user_id = $user_id;
    }
    /**
     * Return an "SHA1 Hashed File Name" of the uploaded file
     *
     * @return true
     *  Return the file real name using getName() function and hash it using SHA1
     */
    public function hashName()
    {
        $this->hash_id = sha1(
            $this->sanitize(
                basename(
                    $this->hash_name
                ) . uniqid()
            )
        );

        $this->hash_name = sha1(
            $this->sanitize(
                basename(
                    $this->hash_name
                ) . uniqid()
            )
        ) . "." . $this->getExtension();

        return true;
    }
    /**
     * Get the date of the uploaded file
     * 
     * @return int|bool
     *  Return the file last modification time or false if an error occurred
     */
    public function getDate()
    {
        return filemtime($this->getTempName());
    }
    /**
     * Function to upload the file to the server
     * 
     * @return bool
     *  Return true if the file is uploaded or false otherwise
     */
    public function upload()
    {
        $filename = $this->hash_name;

        if ($this->moveFile($filename) == true) {
            $this->addLog(['filename' => $this->hash_name, "message" => 0]);
            $this->addFile($this->getJSON($this->hash_name));
            return true;
        }
    }
    /**
     * Function to upload file to the server using chunck system
     * 
     * @param string $filename
     *  The file name you want to use as uploaded name
     * @return bool
     *  Return true if the file is uploaded or false otherwise
     */
    public function moveFile($filename)
    {
        set_time_limit(0);
        $orig_file_size = $this->getSize();
        $chunk_size = 4096;
        $upload_start = 0;
        $handle = fopen($this->getTempName(), "rb");
        $fp = fopen($this->upload_folder['folder_path'] . "/" . $filename, 'w');

        while ($upload_start < $orig_file_size) {

            $contents = fread($handle, $chunk_size);
            fwrite($fp, $contents);

            $upload_start += strlen($contents);
            fseek($handle, $upload_start);
        }

        fclose($handle);

        if (fclose($fp)) {
            return true;
        } else {
            $this->addLog(['filename' => $filename, "message" => 7]);
            return false;
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
     * Function to create an upload folder and secure it
     * 
     * @param string $folder_name
     *  The folder name you want to create as an upload folder
     * @return void
     */
    public function createUploadFolder($folder_name)
    {
        if (!file_exists($folder_name) && !is_dir($folder_name)) {
            @mkdir($this->sanitize($folder_name));
            @chmod($this->sanitize($folder_name), 0777);

            $this->protectFoler($folder_name);
        }

        $this->setUploadFolder([
            "folder_name" => $folder_name,
            "folder_path" => realpath($folder_name)
        ]);
    }

    /**
     * Function to potect a folder
     * 
     * @param string $folder_name
     *  The folder name you want protect
     * @return void
     */
    public function protectFoler($folder_name)
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
        $data = trim($value);
        $data = htmlspecialchars($data, ENT_QUOTES, "UTF-8");
        $data = strip_tags($data);
        $data = filter_var($data, FILTER_SANITIZE_STRING);
        return $data;
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
     * Return the files from the upload folder to view them
     * 
     * @return array|bool
     *  Return an array the contains all files in the upload folder or false if an error occurred
     */
    public function getUploadDirFiles()
    {
        return scandir($this->upload_folder['folder_path']);
    }
    /**
     * Check if a file exist and it is a real file
     *
     * @param string $file_name
     *  The file you want to cheack if it exist 
     * @return bool
     *  Return true if the exist or false otherwise
     */
    public function isFile($file_name)
    {
        $file_name = $this->sanitize($file_name);
        if (file_exists($file_name) && is_file($file_name)) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * Check if a directory exist and it is a real directory
     * 
     * @param string $dir_name
     *  The name of the folder you want check if it exist
     * @return bool
     *  Return true if the folder exist or false otherwise
     */
    public function isDir($dir_name)
    {
        $dir_name = $this->sanitize($dir_name);

        if (is_dir($dir_name) && file_exists($dir_name)) {
            return true;
        } else {
            return false;
        }
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
    public function callback($function, $args = null)
    {
        if (is_callable($function)) {
            if (is_array($args)) {
                return call_user_func_array($function, $args);
            } else {
                return call_user_func($function, $args);
            }
        }
    }
    /**
     * Add a message the system log
     * 
     * @param mixed $id
     *  The array index that you want to assign the message too
     * @param mixed $message
     *  The message id from the messsages array or as raw string
     * @return void
     */
    public function addLog($message, $id = null)
    {
        if ($id == null) {
            array_push($this->logs, $message);
        } else {
            $this->logs[$id] = $message;
        }
    }
    /**
     * Get all logs from system log to view them
     * 
     * @return array
     *  Return an array that contains all the logs in class logs system
     */
    public function getLogs()
    {
        return $this->logs;
    }
    /**
     * Get a system log message by an array index id
     * 
     * @param mixed $log_id
     *  The logs id to retrive the message
     * @return int
     *  Return the message id to use with the messages array 
     */
    public function getLog($log_id)
    {
        return $this->logs[$log_id];
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

        if (is_file("php.ini") == FALSE) {
            touch('php.ini');
        }

        file_put_contents('php.ini', '[PHP]' . "\n" . implode("\n", $sttings));
    }
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
     * Get all the uploaded file information in JSON
     * 
     * @return string
     *  Return a JSON string that contains the uploaded file information
     */
    public function getJSON()
    {
        return json_encode(
            [
                "filename" => $this->hash_name,
                "filehash" => $this->hash_id,
                "filesize" => $this->formatBytes($this->getSize()),
                "uploaddate" => date("Y-m-d h:i:s", $this->getDate()),
                "user_id" => $this->getUserID(),
                "file_id" => $this->getFileID(),
                "qrcode" => $this->generateQrCode(),
                "downloadurl" => $this->generateDownloadLink(),
                "directlink" => $this->generateDirectDownloadLink(),
                "deletelink" => $this->generateDeleteLink(),
            ]
        );
    }
    /**
     * Function to add a file to the files array
     * 
     * @param string $json_string
     *  The JSON string the contains the file information
     * @return void
     */
    public function addFile($json_string)
    {
        array_push($this->files, json_decode($json_string));
    }
    /**
     * Function to return an array with all the uploaded files information
     * 
     * @return array
     */
    public function getFiles()
    {
        return $this->files;
    }
    /**
     * Function to get a log message using a message index id
     *
     * @param int $index
     *  The message index from 1 to 14
     * @return string
     *  Return the log message as string
     */
    public function getMessage($index)
    {
        return $this->message[$index];
    }
    /**
     * Extra Firewall 1: Check an image dimenstions aginst the class dimenstions
     * 
     * @param int $opreation
     *  Filters opreations from 0 to 5
     * @return bool
     *  Return true if an image size passed this filter otherwise otherwise
     */
    public function checkDimenstion($opreation = 2)
    {
        $image_data = getimagesize($this->getTempName());
        $width = $image_data[0];
        $height = $image_data[1];
        switch ($opreation) {
            case 0:
                if ($height <= $this->max_height) {
                    return true;
                } else {
                    $this->addLog(['filename' => $this->hash_name, "message" => 8]);
                }
                break;

            case 1:
                if ($width <= $this->max_width) {
                    return true;
                } else {
                    $this->addLog(['filename' => $this->hash_name, "message" => 9]);
                }
                break;

            case 2:
                if ($width <= $this->max_width && $height <= $this->max_height) {
                    return true;
                } else {
                    $this->addLog(['filename' => $this->hash_name, "message" => 8]);
                }
                break;

            case 3:
                if ($height >= $this->min_height) {
                    return true;
                } else {
                    $this->addLog(['filename' => $this->hash_name, "message" => 10]);
                }
                break;

            case 4:
                if ($width >= $this->min_width) {
                    return true;
                } else {
                    $this->addLog(['filename' => $this->hash_name, "message" => 11]);
                }
                break;

            case 5:
                if ($width >= $this->min_width && $height >= $this->min_height) {
                    return true;
                } else {
                    $this->addLog(['filename' => $this->hash_name, "message" => 12]);
                }
                break;

            default:
                $this->addLog(['filename' => $this->hash_name, "message" => 14]);
                break;
        }
    }
    /**
     * Function to set the miximum class image dimenstions to validate them
     * 
     * @param int $height
     *  The miximum image height
     * @param int $width
     *  The miximum image width
     * @return void
     */
    public function setMaxDimenstion($height = null, $width = null)
    {
        $this->max_height = $height;
        $this->max_width = $width;
    }
    /**
     * Function to set the minimum class image dimenstions to validate them
     * 
     * @param int $height
     *  The minimum image height
     * @param int $width
     *  The minimum image width
     * @return void
     */
    public function setMinDimenstion($height = null, $width = null)
    {
        $this->min_height = $height;
        $this->min_width = $width;
    }
    /**
     * Extra Firewall 2: Function to check if uploaded file is an image
     * 
     * @return bool
     *  Return true if the uploaded file is a real image otherwise false
     */
    public function isImage()
    {
        if (in_array($this->getMime(), [
            'image/gif',
            'image/jpeg',
            'image/pjpeg',
            'image/png'
        ])) {
            return true;
        } else {
            $this->addLog(['filename' => $this->hash_name, "message" => 13]);
        }
    }
    /**
     * Generate a Uniqe ID for each uploaded file
     *
     * @param mixed $prefix
     *  Custom string to append before the unique id
     * @return string
     *  Return the uinque id hashed using sha1
     */
    public function generateFileID()
    {
        return hash("sha1", uniqid("file-"));
    }
    /**
     * Generate a User ID for each uploaded file
     *
     * @return string
     *  Return the user id hashed using sha1
     */
    public function generateUserID($disable_session = false)
    {
        if ($disable_session == true) {
            return hash("sha1", "user-" . bin2hex(random_bytes(16)));
        } else {
            return hash("sha1", "user-" . session_id());
        }
    }
}
