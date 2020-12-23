<?php

namespace Uploady\Handler;

/**
 * PHP Library to help you build your own file sharing website.
 *
 * @version 1.5.2
 * @category File_Upload
 * @package Uploady
 * @author fariscode <farisksa79@gmail.com>
 * @license MIT
 * @link https://github.com/FarisCode511/Uploady
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
     * File Upload Controller
     *
     * @var string
     */
    private $upload_controller;

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
     * Use hashed name insted of the uploaded file name
     *
     * @var boolean
     */
    private $use_hash;

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
     * Enable or Disable file overwriting
     *
     * @var bool
     */
    private $overwrite_file;

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
     * @param boolean $use_hash
     *  Use the hashed file name as the upload name insted of the raw format
     * @param boolean $overwrite_file
     *  Enable it if you want to overwrite the file it the exist on the server
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
        $use_hash = false,
        $overwrite_file = true,
        $max_height = null,
        $max_width = null,
        $min_height = null,
        $min_width = null,
        $file_id = null,
        $user_id = null
    ) {
        // initialize attributes
        $this->upload_input = $upload_input; // Set file input
        $this->upload_folder = $upload_folder; // Set upload folder
        $this->controller = $this->sanitize($controller); // Set the class controller folder
        $this->upload_controller = $this->sanitize($upload_controller); // Set the upload controller | Example => upload.php
        $this->size = $this->sizeInBytes($this->sanitize($size)); // Set limit Size
        $this->use_hash = $use_hash; // use hashed name insted of the raw name
        $this->overwrite_file = $overwrite_file; // Set file overwriting to true to enable file overwriting
        $this->max_height = $max_height; // Set Max Height for an image
        $this->max_width = $max_width; // Set Max Width for an image
        $this->min_height = $min_height; // Set Min Height for an image
        $this->min_width = $min_width; // Set Min Width for an image
        $this->file_id = $file_id; // Set File ID for security
        $this->user_id = $user_id; // Set User ID for security
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
        $this->upload_input = $upload_input; // Set the upload input to a new one
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
        $this->controller = $this->sanitize($controller); // Set the class controller path
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
        $this->upload_controller = realpath($this->sanitize($upload_controller)); // Sanitize and set the upload controller name
    }

    /**
     * Set $use_hash to true or false when needed
     * 
     * @param boolean $use_hash
     *  Set true to use the hashed file name as the upload name insted of the raw format
     * @return void
     */
    public function useHashAsName($use_hash = false)
    {
        $this->use_hash = $use_hash;
    }

    /** 
     * Enable File Uploading Protection and Filters
     * 
     * @return void
     */
    public function enableProtection()
    {

        // Decode JSON and Set Protection Data

        // Enable Level 1 Protection
        $this->name_array = json_decode(
            file_get_contents(
                $this->sanitize(
                    $this->controller . "forbidden.json"
                )
            )
        );

        // Enable Level 2 and 3 Protection
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
        $this->filter_array = $filter_array; // Custom extension filter array
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
        // Set file size limit to a new limit
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
        $this->upload_folder = $folder_name; // Sanitize and set the upload folder when needed
    }

    /**
     * Firewall 1: Check File Extension
     *
     * @return bool
     *  Return true it the uploaded file extenstion is allowed
     */
    public function checkExtension()
    {
        // Check if the file extension is whitelisted
        if (key_exists(str_replace('.', '', $this->getExtension()), $this->filter_array)) {
            return true; // Return true if the extension is not blacklisted
        } else {
            $this->addLog(['filename' => $this->getName(), "message" => 1]); // Show an error message
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
        // Get the file MIME type using the browser
        $mime = mime_content_type($this->getTempName());
        // Check if the file MIME type is whitelisted
        if ($this->filter_array[$this->getExtension()] == $mime) {
            // Check if the browser MIME type equals the server MIME type
            if ($mime == $this->getMime()) {
                return true; // Return true if the MIME type is not blacklisted
            } else {
                $this->addLog(['filename' => $this->getName(), "message" => 1]); // Show an error message
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
        $finfo = finfo_open(FILEINFO_MIME_TYPE); // Open the file to get MIME type
        $mtype = finfo_file($finfo, $this->getTempName()); // get MIME type and add it to a variable
        if (finfo_close($finfo)) {
            return $mtype; // close the file the and return the MIME type
        } else {
            $this->addLog(['filename' => $this->getName(), "message" => 2]); // Show a message error
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
        return $this->upload_input['type']; // Return the MIME type using php default settings
    }

    /**
     * Firewall 3: Check File Name is Forbidden
     * 
     * @return bool
     *  Return true if the name is forbidden
     */
    public function checkForbidden()
    {
        // check if a file name is forbidden
        if (!(in_array($this->getName(), $this->name_array))) {
            return true; // Return true if the name is not forbidden
        } else {
            $this->addLog(['filename' => $this->getName(), "message" => 3]); // Show an error message
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
        // Check if a file size is less or equal the size limit
        if ($this->getSize() <= $this->size) {
            return true; // Return true if the uploaded passed the size limit test
        } else {
            $this->addLog(['filename' => $this->getName(), "message" => 4]); // Show an error message
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
        // check if a the HTML input is empty
        if ($this->upload_input['error'] == UPLOAD_ERR_NO_FILE) {
            $this->addLog(['filename' => $this->getName(), "message" => 5]); // Return false if the input is empty
            return false;
        } else {
            return true; // Return true if the input has a file
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
        return "https://chart.googleapis.com/chart?chs=" . $this->sanitize($qr_size) . "&cht=qr&chl=" . $this->generateDownloadLink() . "&choe=UTF-8"; # Using google charts api to generate a Qr Code with a custom size using $qr_size
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
        // Return a formated download link
        return sprintf(
            "%s/%s?file_id=%s",
            SITE_URL,
            "download.php", // set the download worker file
            $file_id // Get the uploaded file id
        ); // Format String as the download link example => http://localhost/up/download.php?file_id=???????
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

        // Return a formated download link
        return sprintf(
            "%s/%s?file_id=%s&user_id=%s",
            SITE_URL,
            "delete.php", // set the delete worker file
            $file_id, // Uploaded File ID
            $user_id // Uploaded User ID
        ); // Format String as the delete link example => http://localhost/up/delete.php?file_id=???????&user_id==???????&
    }

    /**
     * Generate a direct download link
     * 
     * @return string
     *  Return a well formatted direct download link without a custom download page
     */
    public function generateDirectDownloadLink()
    {
        $filename = ($this->use_hash ?
            $this->hashName() . "." . $this->getExtension() :
            $this->getName());
        // Return a formated download link
        return sprintf(
            "%s/%s/%s",
            SITE_URL, // get the requested url base dir name
            $this->upload_folder['folder_name'], // Get the upload folder
            $filename // Get the uploaded file name
        ); // Format String as the download link example => http://localhost/up/upload/filename.txt
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
     * Generate a simple upload form
     * 
     * @param boolean $multiple
     *  Set true if you want to make the upload input handles multipe files
     * @return string
     *  Return an HTML Upload Form code to use
     */
    public function generateForm($multiple = false)
    {
        $multiple = $multiple ? "multiple" : null;
        // Upload Form Code
        return '
        <form class="upload_form" action="' . $this->upload_controller . '" method="post" enctype="multipart/form-data">
            <input class="upload_input" type="file" name="file" ' . $multiple . ' />
            <button  name="upload" class="upload_button" type="submit">Upload</button>
        </form>
        ';
    }

    /**
     * Generate a multi upload form
     *
     * @param int $size
     *  The number of inputs in the form
     * @return string
     *  Return an HTML Upload Form code to use
     */
    public function generateMultiInput($size = 5)
    {
        $form = '<form class="upload_form" action="' . $this->upload_controller . '" method="post" enctype="multipart/form-data">'; # Form open tag with with settings to make uploading possible
        // Using for loop to create forms based on $size
        for ($i = 0; $i < $size; $i++) {
            // Upload Form Code
            $form .= '
            <div class="container">
                <input class="upload_input" type="file" name="file[]">
            </div>
            ';
        }
        $form .= '<button name="upload" class="upload_button" type="submit">Upload</button>'; // button to upload the file (:
        $form .= '</form>'; // HTML Form closing tag

        return $form;
    }

    /**
     * Return an "SHA1 Hashed File Name" of the uploaded file
     *
     * @return string
     *  Return the file real name using getName() function and hash it using SHA1
     */
    public function hashName()
    {
        return sha1(
            $this->sanitize( // Sanitize the input
                basename(
                    $this->getName()
                )
            )
        ); // Get the file real name using getName() function and hash it using SHA1
    }

    /**
     * Get the date of the uploaded file
     * 
     * @return int|bool
     *  Return the file last modification time or false if an error occurred
     */
    public function getDate()
    {
        return filemtime($this->getTempName()); // Get the temp_name using the function getTempName() and return the date
    }

    /**
     * Function to upload the file to the server
     * 
     * @return bool
     *  Return true if the file is uploaded or false otherwise
     */
    public function upload()
    {
        // check and set the file name depending on $use_hash
        $filename = ($this->use_hash ?
            $this->hashName() . "." . $this->getExtension() :
            $this->getName());
        // Check if OverWrite setting is enabled
        if (!($this->overwrite_file == true)) {
            // Check if the is uploaded to the server
            if ($this->isFile($this->upload_folder['folder_path'] . "/" . $filename) == false) {
                // Function to move the file to the upload folder
                if ($this->moveFile($filename)) {
                    $this->addLog(['filename' => $this->getName(), "message" => 0]);
                    $this->addFile($this->getJSON());
                    return true;
                }
            } else {
                // Show an error message
                $this->addLog(['filename' => $this->getName(), "message" => 6]);
                return false;
            }
        } else {
            // Function to move the file to the upload folder
            if ($this->moveFile($filename) == true) {
                $this->addLog(['filename' => $this->getName(), "message" => 0]);
                $this->addFile($this->getJSON());
                return true;
            }
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
            $this->addLog(['filename' => $this->getName(), "message" => 7]);
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
        $file_array = array(); // An empty array to add the fixed to it
        $file_count = count($file_post['name']); // Count the number of element in the file input
        $file_keys = array_keys($file_post); // get the array keys to loop through it

        # loop through the input and fix it
        for ($i = 0; $i < $file_count; $i++) {
            foreach ($file_keys as $key) {
                // Change the array key placement
                $file_array[$i][$key] = $file_post[$key][$i];
            }
        }
        // Return the fixed array
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
        // Check if a folder exist or not ?
        if (!file_exists($folder_name) && !is_dir($folder_name)) {
            // Create a new dir and set the proper permissions
            @mkdir($this->sanitize($folder_name));
            @chmod($this->sanitize($folder_name), 0777);

            // Protect the folder by adding .htaccess and index.php
            $this->protectFoler($folder_name);
        }

        $this->setUploadFolder(["folder_name" => $folder_name, "folder_path" => realpath($folder_name)]);
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
        // Check if .htaccess does not exist then create a new one with proteciton settings
        if (!file_exists($folder_name . "/" . ".htaccess")) {
            $content = "Options -Indexes" . "\n"; //
            $content .= "<Files .htaccess>" . "\n"; //
            $content .= "Order allow,deny" . "\n"; // HTACCESS FILE CONTENT
            $content .= "Deny from all" . "\n"; //
            $content .= "</Files>"; //
            @file_put_contents($this->sanitize($folder_name) . "/" . ".htaccess", $content); // Write the content to the .htaccess file
        }

        // Forbid Access to the upload folder
        if (!file_exists($this->sanitize($folder_name) . "/" . "index.php")) {
            $content = "<?php http_response_code(403); ?>"; // "Enable Forbidden"
            @file_put_contents($this->sanitize($folder_name) . "/" . "index.php", $content); // Write the "Enable Forbidden" Code to a new file
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
        // Out-Of-TheBox Filtering and Sanitizing
        $data = trim($value); // Remove White Spaces
        $data = htmlspecialchars($data, ENT_QUOTES, "UTF-8"); // Convert characters to HTML entities
        $data = strip_tags($data); // Strip HTML and PHP Tags
        $data = filter_var($data, FILTER_SANITIZE_STRING); // filters a variable with a string filter
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
        // Out-of-TheBox Byte Convertor
        $units = array('B', 'KB', 'MB', 'GB', 'TB'); // Array to set the current names of storage types
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
        // Check if $file_name is a file and is exists on the server
        if (file_exists($file_name) && is_file($file_name)) {
            return true; // Return true if yes
        } else {
            return false; // Return true if yes
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

        // Check if $dir_name is a directory and is exists on the server
        if (is_dir($dir_name) && file_exists($dir_name)) {
            return true; // Return true if yes
        } else {
            return false; // Return false if no
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
        // check if the function is callable
        if (is_callable($function)) {
            // check if $args is an array
            if (is_array($args)) {
                return call_user_func_array($function, $args); // Create a user function with multiple args
            } else {
                return call_user_func($function, $args); // Create a user function with a single args
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
            array_push($this->logs, $message); // yes then just push the message
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
        return $this->logs; // Return the system logs array
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
     * Set file overwriting to true or false
     * 
     * @param boolean $status
     *  Set true if you want to enable overwriting or false otherwise
     * @return void
     */
    public function setFileOverwriting($status)
    {
        $this->overwrite_file = $status; // Set file overwriting to true or false

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
        // Loop through $ini_settings
        foreach ($ini_settings as $key => $value) {
            ini_set($key, $value); // Set ini_set using $key and $value

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
        // Return an the informations Array as JSON Encoded string
        return json_encode(
            [
                "filename" => $this->use_hash ?
                    $this->hashName() . "." . $this->getExtension() :
                    $this->getName(),
                "filehash" => $this->hashName(),
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
        return $this->message[$index]; // Return a message from $message array using $index
    }

    /**
     * Include Bootstrap CSS
     * 
     * @return string
     *  Return Bootstrap files using CDN
     */
    public function includeBootstrap()
    {
        // return Bootstrap files using CDN
        return '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" />';
    }

    /**
     * Include jQuery Javascript files
     * 
     * @return string
     *  Return jQuery files using CDN
     */
    public function includeJquery()
    {
        // return jQuery files using CDN
        return '<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" ></script>
				<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" ></script>
				<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" ></script>';
    }

    /** 
     * Function to create an upload worker using one line of code
     * 
     * @param array $upload_input
     *  An array of the upload file information coming from $_FILES
     * @return bool
     *  Return true if the file is uploaded otherwise false
     */
    public function factory($upload_input = null)
    {
        // set upload folder to "uploads"
        $this->setUploadFolder([
            "folder_name" => "uploads",
            "folder_path" => \realpath("uploads"),
        ]);
        $this->setFileOverwriting(true); // Set file overwriting to true
        $this->useHashAsName(false); // Set using hash name to false to use the file real name
        $this->enableProtection(); // Enable class 3 firewall levels
        $this->file_id = $this->generateFileID();
        $this->user_id = $this->generateUserID();
        if ($upload_input == null) {
            $this->setUpload($_FILES['file']); // check if $upload_input is null then set the upload input to $_FILES['file']
        } else {
            $this->setUpload($upload_input); // else set the upload input to your defined input
        }
        // Check all class 5 protection levels
        if ($this->checkIfEmpty()) {
            if ($this->checkSize()) {
                if (
                    $this->checkForbidden() &&
                    $this->checkExtension() &&
                    $this->checkMime()
                ) {
                    // Upload the file (:
                    return $this->upload();
                }
            }
        }
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
                    $this->addLog(['filename' => $this->getName(), "message" => 8]);
                }
                break;

            case 1:
                if ($width <= $this->max_width) {
                    return true;
                } else {
                    $this->addLog(['filename' => $this->getName(), "message" => 9]);
                }
                break;

            case 2:
                if ($width <= $this->max_width && $height <= $this->max_height) {
                    return true;
                } else {
                    $this->addLog(['filename' => $this->getName(), "message" => 8]);
                }
                break;

            case 3:
                if ($height >= $this->min_height) {
                    return true;
                } else {
                    $this->addLog(['filename' => $this->getName(), "message" => 10]);
                }
                break;

            case 4:
                if ($width >= $this->min_width) {
                    return true;
                } else {
                    $this->addLog(['filename' => $this->getName(), "message" => 11]);
                }
                break;

            case 5:
                if ($width >= $this->min_width && $height >= $this->min_height) {
                    return true;
                } else {
                    $this->addLog(['filename' => $this->getName(), "message" => 12]);
                }
                break;

            default:
                $this->addLog(['filename' => $this->getName(), "message" => 14]);
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
        if (in_array($this->getMime(), ['image/gif', 'image/jpeg', 'image/pjpeg', 'image/png'])) {
            return true;
        } else {
            $this->addLog(['filename' => $this->getName(), "message" => 13]);
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
    public function generateUserID($disbale_session = false)
    {
        if ($disbale_session == true) {
            return hash("sha1", "user-" . bin2hex(random_bytes(16)));
        } else {
            return hash("sha1", "user-" . session_id());
        }
    }
}
