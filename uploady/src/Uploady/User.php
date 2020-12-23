<?php

namespace Uploady;

/**
 *  A Class to Handle User Data
 *
 * @package Uploady
 * @version 1.5.2
 * @author fariscode <farisksa79@gmail.com.com>
 * @license MIT
 * @link https://github.com/FarisCode511/Uploady
 */
class User
{
    /**
     * Database Connection
     *
     * @var Database
     */
    private $db;

    /**
     * User class constructor
     *
     * @param object $database
     *  An object from the Database class
     * @return void
     */
    public function __construct($database)
    {
        $this->db = $database;
    }

    /**
     * Get all existing users from the database
     *
     * @return array|bool
     */
    public function getUsers()
    {
        $sql = "SELECT * FROM users;";

        $this->db->query($sql);

        if ($this->db->execute()) {
            return $this->db->resultset();
        } else {
            return false;
        }
    }

    /**
     * Function to get the user information
     *
     * @param string $username
     *  The username you want to get his/her information
     * @return object|bool
     *  An object contains the username information otherwise false
     */
    public function getUserData($username)
    {
        $find_by = "";

        if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
            $find_by = "email";
        } elseif (is_int($username)) {
            $find_by = "id";
        } else {
            $find_by = "username";
        }

        $sql = sprintf("SELECT * FROM users WHERE %s = %s;", $find_by, ":" . $find_by);

        $this->db->query($sql);

        $this->db->bind(":" . $find_by, $username, \PDO::PARAM_STR);

        if ($this->db->execute()) {
            return $this->db->single();
        } else {
            return false;
        }
    }

    /**
     * Function to know how many users
     *
     * @return int
     *  Return the number of users
     */
    public function numUsers()
    {
        $this->db->query("SELECT * FROM users;");

        if ($this->db->execute()) {
            return $this->db->rowCount();
        }
    }

    /**
     * Function to check if a user exists in the database
     *
     * @param string $username
     *  The username you want to check if it exists
     * @return bool
     *  Return true if the user exists otherwise return false
     */
    public function checkUser($username)
    {
        $find_by = "";

        if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
            $find_by = "email";
        } elseif (is_int($username)) {
            $find_by = "id";
        } else {
            $find_by = "username";
        }

        $this->db->query(sprintf("SELECT * FROM users WHERE %s = %s;", $find_by, ":" . $find_by));

        $this->db->bind(":" . $find_by, $username, \PDO::PARAM_STR);

        if ($this->db->execute()) {
            if ($this->db->rowCount()) {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * Create a new a user when needed
     *
     * @param array $user_array
     * @return bool
     */
    public function createUser($user_array)
    {
        $sql = sprintf(
            "INSERT INTO users (%s) VALUES (%s)",
            implode(", ", array_keys($user_array)),
            ":" . implode(",:", array_keys($user_array))
        );

        $this->db->query($sql);

        foreach ($user_array as $key => $value) {
            $this->db->bind(":" . $key, $value);
        }

        return $this->db->execute();
    }

    /**
     * Update user information when needed
     *
     * @param int $id
     *  The user id you want to update it information
     * @param array $user_array
     *  The new user data that you want to change to
     * @return bool
     *  Return true if user data is updated
     */
    public function updateUser($id, $user_array)
    {

        $array_keys = array_keys($user_array);

        $upate_user_syntax = "UPDATE users SET %s WHERE id = :id;";

        $sql_values = "";

        foreach ($array_keys as $key) {
            $sql_values .= $key . "=" . ":" . $key . ",";
        }

        $sql_values = rtrim($sql_values, ",");

        $this->db->query(sprintf(
            $upate_user_syntax,
            $sql_values
        ));

        foreach ($user_array as $key => $value) {
            $this->db->bind(":" . $key, $value, \PDO::PARAM_STR);
        }

        $this->db->bind(":id", $id, \PDO::PARAM_INT);

        return $this->db->execute();
    }

    /**
     * Delete the username from the database when needed
     *
     * @param string $username
     *  The username you want to delete from the database
     * @return bool
     *  Return true if the username is deleted otherwise false
     */
    public function deleteUser($username)
    {
        $find_by = "";

        if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
            $find_by = "email";
        } elseif (is_int($username)) {
            $find_by = "id";
        } else {
            $find_by = "username";
        }

        $sql = sprintf("DELETE FROM users WHERE %s = :find_by", $find_by);

        $this->db->query($sql);

        $this->db->bind(":find_by", $username);

        return $this->db->execute();
    }

    /**
     * Get a username using the user_id
     *
     * @param string $user_id
     *  The user_id generated from the UploadHandler
     * @return string
     *  Return the username from the database
     */
    public function getUsernameByUserId($user_id)
    {
        $sql = "SELECT username FROM users WHERE user_id = :user_id";

        $this->db->query($sql);

        $this->db->bind(':user_id', trim($user_id), \PDO::PARAM_STR);

        if ($this->db->execute()) {
            $obj =  $this->db->single();

            if ($obj != false) {
                return $obj->username;
            } else {
                return $user_id;
            }
        }
    }

    public function activateAccount($token)
    {
        $sql = 'SELECT * FROM users WHERE activation_hash = :hash';

        $hash = sha1($token);

        $this->db->query($sql);

        $this->db->bind(':hash', $hash, \PDO::PARAM_STR);

        if ($this->db->execute()) {
            $obj = $this->db->single();

            if ($obj != false) {
                $sql = "UPDATE users SET is_active = :bool, activation_hash = :hash WHERE activation_hash = :old";

                $this->db->query($sql);

                $this->db->bind(':bool', 1, \PDO::PARAM_INT);
                $this->db->bind(":hash", null, \PDO::PARAM_NULL);
                $this->db->bind(":old", $hash, \PDO::PARAM_STR);

                if ($this->db->execute()) {
                    return true;
                }
            } else {
                return false;
            }
        }
    }
}
