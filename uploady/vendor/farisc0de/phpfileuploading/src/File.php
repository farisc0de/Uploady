<?php

namespace Farisc0de\PhpFileUploading;

/**
 * File Representation Class
 *
 * @version 1.5.3
 * @category File_Upload
 * @package PhpFileUploading
 * @author fariscode <farisksa79@gmail.com>
 * @license MIT
 * @link https://github.com/farisc0de/PhpFileUploading
 */

class File
{
    /**
     * File Input
     *
     * @var array
     */
    private $file;
    /**
     * Utility Class
     *
     * @var Utility
     */
    private $utility;
    /**
     * Class Constructor to initialize attributes
     *
     * @param array $file
     *  An array of the upload file information coming from $_FILES
     * @return void
     */
    public function __construct($file, $utility)
    {
        $this->file = $file;
        $this->utility = $utility;
    }

    /**
     * Return the size of the uploaded file as bytes
     *
     * @return float
     *  Return the uploaded file size as bytes
     */
    public function getSize()
    {
        return $this->utility->fixintOverflow($this->file['size']);
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
     * Function to get the MIME type using the server
     *
     * @return string
     *  Return the file MIME type as string
     */
    public function getMime()
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mtype = finfo_file($finfo, $this->getTempName());

        if (!finfo_close($finfo)) {
            return "application/octet-stream";
        }

        return $mtype;
    }

    /**
     * Function that return the uploaded file MIME type
     *
     * @return string
     *  Return the file MIME type as string
     */
    public function getFileType()
    {
        return $this->file['type'];
    }

    /**
     * Return the name of the uploaded file
     *
     * @return string
     *  Return the name of the uploaded file as string
     */
    public function getName()
    {
        return $this->file['name'];
    }

    /**
     * Return the PHP Generated name for the uploaded file
     *
     * @return string
     *  return the temp name that PHP generated for uploaded file
     */
    public function getTempName()
    {
        return $this->file['tmp_name']; // Return the PHP Generated Temp name
    }

    /**
     * Function to check if the file is empty or not
     *
     * @return bool
     *  Return true if the the file contain nothing
     */
    public function isEmpty()
    {
        if ($this->file['error'] == UPLOAD_ERR_NO_FILE) {
            return true;
        }

        return false;
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
     * Get the file hash
     *
     * @return string
     *  Return the file hash as string
     */
    public function getFileHash()
    {
        return  hash_file('sha1', $this->getTempName());
    }
}
