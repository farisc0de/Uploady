<?php

namespace Uploady\Handler;

/**
 * Class to Handle adding files to the database
 *
 * @package Uploady
 * @version 1.5.3
 * @author fariscode <farisksa79@gmail.com>
 * @license MIT
 * @link https://github.com/farisc0de/Uploady
 */
class UploadHandler
{
    /**
     * Database Object
     *
     * @var \Uploady\Database
     */
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function addFile($file_id, $user_id, $file_data)
    {
        $this->db->query(
            "INSERT INTO files (
                file_id,user_id,file_data,uploaded_at) 
                VALUES
                (:file_id,:user_id,:file_data,:uploaded_at)"
        );

        $data = json_decode($file_data);

        $this->db->bind(":file_id", $file_id, \PDO::PARAM_STR);
        $this->db->bind(":user_id", $user_id, \PDO::PARAM_STR);
        $this->db->bind(":file_data", $file_data, \PDO::PARAM_STR);
        $this->db->bind(":uploaded_at", $data->uploaddate, \PDO::PARAM_STR);

        return $this->db->execute();
    }

    public function fileExist($file_id)
    {
        $this->db->query("SELECT * FROM files WHERE file_id = :id");

        $this->db->bind(":id", $file_id, \PDO::PARAM_STR);

        if ($this->db->execute()) {
            if ($this->db->rowCount()) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function userExist($user_id)
    {
        $this->db->query("SELECT * FROM files WHERE user_id = :id");

        $this->db->bind(":id", $user_id, \PDO::PARAM_STR);

        if ($this->db->execute()) {
            if ($this->db->rowCount()) {
                return true;
            } else {
                return false;
            }
        }
    }
    public function getFile($file_id)
    {
        $this->db->query("SELECT file_data FROM files WHERE file_id = :id");

        $this->db->bind(":id", $file_id, \PDO::PARAM_STR);

        if ($this->db->execute()) {
            return $this->db->single();
        }
    }

    public function updateFile($file_id, $user_id, $file_data)
    {
        $this->db->query(
            "UPDATE files SET user_id = :user_id, file_data = :file_data WHERE file_id = :file_id"
        );

        $this->db->bind(":file_id", $file_id, \PDO::PARAM_STR);
        $this->db->bind(":user_id", $user_id, \PDO::PARAM_STR);
        $this->db->bind(":file_data", $file_data, \PDO::PARAM_STR);

        if ($this->db->execute()) {
            return true;
        }
    }

    public function deleteFile($file_id, $user_id)
    {
        $this->db->query("DELETE FROM files WHERE file_id = :id AND user_id = :uid");

        $this->db->bind(":id", $file_id, \PDO::PARAM_STR);
        $this->db->bind(":uid", $user_id, \PDO::PARAM_STR);

        return $this->db->execute();
    }

    public function deleteFileAsAdmin($file_id)
    {
        $this->db->query("DELETE FROM files WHERE file_id = :id");

        $this->db->bind(":id", $file_id, \PDO::PARAM_STR);

        return $this->db->execute();
    }

    public function getFiles()
    {
        $this->db->query('SELECT * FROM files');

        if ($this->db->execute()) {
            return $this->db->resultset();
        }
    }

    public function getFilesById($user_id)
    {
        $this->db->query('SELECT * FROM files WHERE user_id = :uid');

        $this->db->bind(':uid', $user_id, \PDO::PARAM_STR);

        if ($this->db->execute()) {
            return $this->db->resultset();
        }
    }

    public function addDownload($file_id)
    {
        $this->db->query("UPDATE files SET downloads = (downloads + 1) WHERE file_id = :file_id");

        $this->db->bind(":file_id", $file_id, \PDO::PARAM_STR);

        return $this->db->execute();
    }

    public function getDownloadsTotal()
    {
        $this->db->query("SELECT downloads FROM files");

        $this->db->execute();

        $total = 0;

        foreach ($this->db->resultset() as $download) {
            $total += $download->downloads;
        }

        return $total;
    }

    public function getLatestFiles()
    {
        $this->db->query("SELECT * FROM files ORDER BY uploaded_at DESC LIMIT 10");

        $this->db->execute();

        return $this->db->resultset();
    }

    /**
     * Function to know how many files
     *
     * @return int
     *  Return the number of files
     */
    public function countFiles()
    {
        $this->db->query("SELECT * FROM files;");

        if ($this->db->execute()) {
            return $this->db->rowCount();
        }
    }
}
