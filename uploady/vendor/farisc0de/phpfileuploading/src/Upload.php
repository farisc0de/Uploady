<?php

namespace Farisc0de\PhpFileUploading;

/**
 * PHP Library to help you build your own file sharing website.
 *
 * @version 1.5.3
 * @category File_Upload
 * @package PhpFileUploading
 * @author fariscode <farisksa79@gmail.com>
 * @license MIT
 * @link https://github.com/farisc0de/PhpFileUploading
 */

final class Upload
{
    /**
     * File object
     * @var File|null
     */
    private $file;
    /**
     * Utility object
     * @var Utility|null
     */
    private $util;
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
     * File name
     *
     * @var int
     */
    private $file_name;
    /**
     * File hash
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
     * Website URL to use with Download URL
     *
     * @var string
     */
    private $site_url;
    /**
     * Check if the hash name function is triggerd
     * @var mixed
     */
    private $is_hashed = false;
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
     * @param File $file
     *  An array of the upload file information coming from $_FILES
     * @param Utility $utility
     *  Utility Class injection to access helpful functions
     * @param array $upload_folder
     *  An array that contain the upload folder full path and name
     * @param string $controller
     *  The folder name of folder that contains the json filters and the class file
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
     *  A unique if for the file to validate that the file exist
     * @param string $user_id
     *  A unique if for the user to validate the file owner
     * @return void
     */
    public function __construct(
        $util,
        $file = null,
        $upload_folder = [],
        $site_url = '',
        $size = "5 GB",
        $max_height = null,
        $max_width = null,
        $min_height = null,
        $min_width = null,
        $file_id = null,
        $user_id = null
    ) {
        $this->util = $util;
        $this->file = $file;
        $this->upload_folder = $upload_folder;
        $this->site_url = $this->util->sanitize($site_url);
        $this->size = $this->util->sizeInBytes($this->util->sanitize($size));
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
     * @param object $file
     *  An array of the upload file information coming from $_FILES
     * @return void
     */
    public function setUpload($file)
    {
        $this->file = $file;
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
                $this->util->sanitize(
                    realpath(__DIR__) . DIRECTORY_SEPARATOR . "filter.json"
                ),
            ),
            true
        )['forbidden'];

        $this->filter_array = json_decode(
            file_get_contents(
                realpath(__DIR__) . DIRECTORY_SEPARATOR . "filter.json"
            ),
            true
        )['extensions'];
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
        $this->name_array = $forbidden_array;
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
        $this->size = $this->util->fixintOverflow(
            $this->util->sizeInBytes(
                $this->util->sanitize(
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
    public function createUserCloud($main_upload_folder = null)
    {
        $user_id = $this->getUserID();

        if ($main_upload_folder == null) {
            $main_upload_folder = $this->upload_folder;
        }

        $user_cloud = $main_upload_folder .
            DIRECTORY_SEPARATOR .
            $user_id;

        if (!file_exists($user_cloud)) {
            @mkdir($user_cloud);
            @chmod($user_cloud, 777);
        }

        return true;
    }

    /** 
     * Get current user folder path
     * 
     * @return string
     *  Return the current user folder path
     */
    /**
     * Get the current user cloud folder
     *
     * @param string $main_upload_folder
     * The main upload folder
     *
     * @return string
     */
    public function getUserCloud($main_upload_folder = null)
    {
        $user_id = $this->getUserID();

        if ($main_upload_folder == null) {
            $main_upload_folder = $this->upload_folder;
        }

        $user_cloud = $main_upload_folder .
            DIRECTORY_SEPARATOR .
            $user_id;

        return $user_cloud;
    }

    /**
     * Check File Extension
     *
     * @return bool
     *  Return true it the uploaded file extenstion is allowed
     */
    public function checkExtension()
    {
        if (!key_exists($this->file->getExtension(), $this->filter_array)) {
            $this->addLog(['filename' => $this->file_name, "message" => 1]);

            return false;
        }

        return true;
    }

    /**
     * Check File MIME Type
     *
     * @return bool
     *  Return true if the uploaded file MIME type is allowed
     */
    public function checkMime()
    {
        $mime = mime_content_type($this->file->getTempName());

        if ($this->filter_array[$this->file->getExtension()] == $mime) {
            if (!$mime == $this->file->getMime()) {
                $this->addLog(['filename' => $this->file_name, "message" => 1]);
                return false;
            }
        }

        return true;
    }

    /**
     * Check File Name is Forbidden
     *
     * @return bool
     *  Return true if the name is forbidden
     */
    public function checkForbidden()
    {
        if ((in_array($this->file_name, $this->name_array))) {
            $this->addLog(['filename' => $this->file_name, "message" => 3]);
            return false;
        }

        return true;
    }

    /**
     * Check file size limit
     *
     * @return bool
     *  Return true if the uploaded file size does not exceed the limit
     */
    public function checkSize()
    {
        if (!($this->file->getSize() <= $this->size)) {
            $this->addLog(['filename' => $this->file_name, "message" => 4]);
            return false;
        }

        return true;
    }

    /**
     * Check an image dimenstions aginst the class dimenstions
     *
     * @param int $opreation
     *  Filters opreations from 0 to 5
     * @return bool
     *  Return true if an image size passed this filter otherwise false
     */
    public function checkDimenstion($opreation = 2)
    {
        $image_data = getimagesize($this->file->getTempName());
        $width = $image_data[0];
        $height = $image_data[1];

        switch ($opreation) {
            case 0:
                if (!($height <= $this->max_height)) {
                    $this->addLog(['filename' => $this->file_name, "message" => 8]);
                    return false;
                }

                return true;
                break;

            case 1:
                if (!($width <= $this->max_width)) {
                    $this->addLog(['filename' => $this->file_name, "message" => 9]);
                    return false;
                }

                return true;
                break;

            case 2:
                if (($width <= $this->max_width && $height <= $this->max_height)) {
                    $this->addLog(['filename' => $this->file_name, "message" => 8]);
                    return false;
                }

                return true;
                break;

            case 3:
                if (!($height >= $this->min_height)) {
                    $this->addLog(['filename' => $this->file_name, "message" => 10]);
                    return false;
                }

                return true;
                break;

            case 4:
                if (!($width >= $this->min_width)) {
                    $this->addLog(['filename' => $this->file_name, "message" => 11]);
                    return false;
                }

                return true;
                break;

            case 5:
                if (!($width >= $this->min_width && $height >= $this->min_height)) {
                    $this->addLog(['filename' => $this->file_name, "message" => 12]);
                    return false;
                }

                return true;
                break;

            default:
                $this->addLog(['filename' => $this->file_name, "message" => 14]);
                break;
        }
    }

    /**
     * Function to check if uploaded file is an image
     *
     * @return bool
     *  Return true if the uploaded file is a real image otherwise false
     */
    public function isImage()
    {
        $image_mime = [
            'image/gif',
            'image/jpeg',
            'image/pjpeg',
            'image/png'
        ];

        if (!in_array($this->file->getMime(), $image_mime)) {
            $this->addLog(['filename' => $this->file_name, "message" => 13]);
            return false;
        }

        return true;
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
     * Function to check if the HTML input is empty or not
     *
     * @return bool
     *  Return true if the the input contain a file false otherwise
     */
    public function checkIfNotEmpty()
    {
        if ($this->file->isEmpty()) {
            $this->addLog(['filename' => $this->file_name, "message" => 5]);
            return false;
        }

        return true;
    }

    /**
     * Generate a Qr Code of the download url
     *
     * @return string
     *  Return the qr code image url to display
     */
    public function generateQrCode()
    {
        return "https://quickchart.io/qr?text=" .
            $this->generateDownloadLink() .
            "&size=150";
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
            $this->site_url,
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
            $this->site_url,
            "delete.php",
            $file_id,
            $user_id
        );
    }

    /**
     * Generate a edit link
     *
     * @return string
     *  Return a well formatted edit file link with a custom edit page
     */
    public function generateEditLink()
    {
        // Get user paramters [ file_id, user_id ]
        $file_id = $this->file_id;
        $user_id = $this->user_id;

        return sprintf(
            "%s/%s?file_id=%s&user_id=%s",
            $this->site_url,
            "edit.php",
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
        $filename = ($this->file_name);

        return sprintf(
            "%s/%s/%s",
            $this->site_url,
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
     * Set the site url manualy when needed to generate links
     *
     * @param string $site_url
     *  The site url you want to genearate urls for
     * @return void
     */
    public function setSiteUrl($site_url = "")
    {

        $this->site_url = $site_url;
    }

    /**
     * Return an "SHA1 Hashed File Name" of the uploaded file
     *
     * @return true
     *  Return the file real name using getName() function and hash it using SHA1
     */
    public function hashName()
    {
        $this->file_name = sha1($this->file->getFileHash() .
            uniqid()) .
            ".{$this->file->getExtension()}";
        $this->is_hashed = true;

        return true;
    }

    /**
     * Function to upload the file to the server
     *
     * @return bool
     *  Return true if the file is uploaded or false otherwise
     */
    public function upload()
    {
        if ($this->file_name == null) {
            $this->file_name = $this->file->getName();
        }

        if ($this->hash_id == null) {
            $this->hash_id = $this->file->getFileHash();
        }

        if ($this->file->getFileHash() != $this->hash_id && $this->is_hashed == false) {
            $this->file_name = $this->file->getName();
            $this->hash_id = $this->file->getFileHash();
        }

        $this->hash_id = $this->file->getFileHash();
        $filename = $this->file_name;

        if ($this->moveFile($filename) == true) {
            $this->addLog(['filename' => $this->file_name, "message" => 0]);
            $this->addFile($this->getJSON($this->file_name));
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
        if (strpos(ini_get('disable_functions'), 'set_time_limit') === false) {
            set_time_limit(0);
        }

        $orig_file_size = $this->file->getSize();
        $chunk_size = 4096;
        $upload_start = 0;
        $handle = fopen($this->file->getTempName(), "rb");
        $fp = fopen($this->upload_folder['folder_path'] . "/" . $filename, 'w');

        stream_set_timeout($handle, $chunk_size, 0);
        stream_set_timeout($fp, $chunk_size, 0);

        while ($upload_start < $orig_file_size) {
            $contents = fread($handle, $chunk_size);
            fwrite($fp, $contents);

            $upload_start += strlen($contents);
            fseek($handle, $upload_start);
        }

        fclose($handle);

        if (!fclose($fp)) {
            $this->addLog(['filename' => $filename, "message" => 7]);
            return false;
        }

        return true;
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
            @mkdir($this->util->sanitize($folder_name));
            @chmod($this->util->sanitize($folder_name), 0777);

            $this->util->protectFolder($folder_name);
        }

        $this->setUploadFolder([
            "folder_name" => $folder_name,
            "folder_path" => realpath($folder_name)
        ]);
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
        $file_name = $this->util->sanitize($file_name);
        return file_exists($file_name) && is_file($file_name);
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
        $dir_name = $this->util->sanitize($dir_name);

        return is_dir($dir_name) && file_exists($dir_name);
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
        if (!($id == null)) {
            $this->logs[$id] = $message;
            return;
        }

        array_push($this->logs, $message);
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
     * Get all the uploaded file information in JSON
     *
     * @return string
     *  Return a JSON string that contains the uploaded file information
     */
    public function getJSON()
    {
        $data = [
            "filename" => $this->file_name,
            "filehash" => $this->hash_id,
            "filesize" => $this->util->formatBytes($this->file->getSize()),
            "uploaddate" => date("Y-m-d h:i:s", $this->file->getDate()),
            "user_id" => $this->getUserID(),
            "file_id" => $this->getFileID(),
        ];

        if ($this->site_url != '') {
            $data['qrcode'] = $this->generateQrCode();
            $data['downloadlink'] = $this->generateDownloadLink();
            $data['directlink'] = $this->generateDirectDownloadLink();
            $data['deletelink'] = $this->generateDeleteLink();
            $data['editlink'] = $this->generateEditLink();
        }

        return json_encode($data);
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
        array_push($this->files, json_decode($json_string, true));
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
     * Generate a Uniqe ID for each uploaded file
     *
     * @param mixed $prefix
     *  Custom string to append before the unique id
     * @return string
     *  Return the uinque id hashed using sha1
     */
    public function generateFileID()
    {
        $this->file_id = hash("sha1", uniqid("file-"));
    }

    /**
     * Generate a User ID for each uploaded file
     *
     * @return bool
     *  Return the user id hashed using sha1
     */
    public function generateUserID($disable_session = false)
    {
        if ($disable_session == true) {
            $this->user_id = hash("sha1", "user-" . bin2hex(random_bytes(16)));
            return true;
        }

        $this->user_id = (isset($_SESSION['user_id'])) ?
            $_SESSION['user_id'] :
            hash("sha1", "user-" . session_id());

        return true;
    }

    /**
     * Inject a dependency class to the main class
     *
     * @param string $class_name
     *  The name of the class to inject
     * @param object $class
     *  The class object to inject
     *
     * @return void
     */
    public function injectClass($class_name, $class)
    {
        $this->$class_name = $class;
    }
}
