<?php

namespace Uploady;

/**
 *  A Class to Handle User Data
 *
 * @package Uploady
 * @version 3.0.x
 * @author fariscode <farisksa79@gmail.com>
 * @license MIT
 * @link https://github.com/farisc0de/Uploady
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
     * Object from Utils Class
     *
     * @var Utils
     */
    private $utils;

    /**
     * User class constructor
     *
     * @param object $database
     *  An object from the Database class
     * @return void
     */
    public function __construct($database, $utils)
    {
        $this->db = $database;

        $this->utils = $utils;
    }

    /**
     * Get all existing users from the database
     *
     * @return array|bool
     */
    public function getAll()
    {
        $sql = $this->utils->escape("SELECT * FROM users;");

        $this->db->prepare($sql);

        return $this->db->execute() ? $this->db->resultset() : false;
    }

    /**
     * Function to get the user information
     *
     * @param string $username
     *  The username you want to get his/her information
     * @return object|bool
     *  An object contains the username information otherwise false
     */
    public function get($username)
    {
        $find_by = $this->findBy($username);

        $sql = sprintf(
            $this->utils->escape("SELECT * FROM users WHERE %s = %s;"),
            $find_by,
            ":" . $find_by
        );

        $this->db->prepare($sql);

        $this->db->bind(":" . $find_by, $username, \PDO::PARAM_STR);

        return $this->db->execute() ? $this->db->single() : false;
    }

    /**
     * Function to know how many users
     *
     * @return int
     *  Return the number of users
     */
    public function countAll()
    {
        $query = $this->utils->escape("SELECT * FROM users;");

        $this->db->prepare($query);

        if ($this->db->execute()) {
            return $this->db->rowCount();
        }

        return 0;
    }

    /**
     * Function to check if a user exists in the database
     *
     * @param string $username
     *  The username you want to check if it exists
     * @return bool
     *  Return true if the user exists otherwise return false
     */
    public function isExist($username)
    {
        $find_by = $this->findBy($username);

        $this->db->prepare(sprintf(
            $this->utils->escape("SELECT * FROM users WHERE %s = %s;"),
            $find_by,
            ":" . $find_by
        ));

        $this->db->bind(":" . $find_by, $username, \PDO::PARAM_STR);

        if ($this->db->execute()) {
            return $this->db->rowCount() > 0;
        }

        return false;
    }

    /**
     * Create a new a user when needed
     *
     * @param array $user_array
     * @return bool
     */
    public function add($user_array)
    {
        $sql = sprintf(
            "INSERT INTO users (%s) VALUES (%s)",
            implode(", ", array_keys($user_array)),
            ":" . implode(",:", array_keys($user_array))
        );

        $this->db->prepare($sql);

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
    public function update($id, $user_array)
    {
        unset($user_array['api_key']);

        $array_keys = array_keys($user_array);

        $upate_user_syntax = "UPDATE users SET %s WHERE id = :id;";

        $sql_values = "";

        foreach ($array_keys as $key) {
            $sql_values .= $key . "=" . ":" . $key . ",";
        }

        $sql_values = rtrim($sql_values, ",");

        $this->db->prepare(sprintf(
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
    public function delete($username)
    {
        $sql = sprintf("DELETE FROM users WHERE %s = :find_by", $this->findBy($username));

        $this->db->prepare($sql);

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
    public function getByUserId($user_id)
    {
        $sql = "SELECT username FROM users WHERE user_id = :user_id";

        $this->db->prepare($sql);

        $this->db->bind(':user_id', trim($user_id), \PDO::PARAM_STR);

        if ($this->db->execute()) {
            $obj =  $this->db->single();
            return ($obj != false) ? $obj->username : $user_id;
        }

        return "";
    }

    /**
     * Activate the user account
     *
     * @param mixed $token
     *  The token generated from the email
     * @return bool|void
     *  Return true if the user is activated otherwise false
     * @throws \PDOException
     *  Throw PDOException if the query fails
     */
    public function activate($token)
    {
        $sql = 'SELECT * FROM users WHERE activation_hash = :hash';

        $hash = hash("sha256", $token);

        $this->db->prepare($sql);

        $this->db->bind(':hash', $hash, \PDO::PARAM_STR);

        if ($this->db->execute()) {
            $obj = $this->db->single();

            if ($obj != false) {
                $sql = "UPDATE users SET is_active = :bool, activation_hash = :hash WHERE activation_hash = :old";

                $this->db->prepare($sql);

                $this->db->bind(':bool', 1, \PDO::PARAM_INT);
                $this->db->bind(":hash", null, \PDO::PARAM_NULL);
                $this->db->bind(":old", $hash, \PDO::PARAM_STR);

                if ($this->db->execute()) {
                    return true;
                }
            }

            return false;
        }
    }

    /**
     * Find a user using diffrent search values
     *
     * @param string $username
     * @return string
     */
    private function findBy($username)
    {
        $find_by = "";

        if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
            $find_by = "email";
        } elseif (is_int($username)) {
            $find_by = "id";
        } else {
            $find_by = "username";
        }

        return $find_by;
    }

    /**
     * Get the user information using the api key
     *
     * @param string $key
     *  The api key you want to get the user information
     * @return object|bool
     *  Return the user information otherwise false
     */
    public function getByApiKey(string $key)
    {
        $this->db->prepare("SELECT * FROM users WHERE api_key = :api_key");

        $this->db->bind(":api_key", $key, \PDO::PARAM_STR);

        $this->db->execute();

        return $this->db->single();
    }

    /**
     * Check if the user has enabled the two factor authentication
     * @param mixed $username
     *  The username you want to check if the two factor authentication is enabled
     * @return int
     *  Return 1 if the two factor authentication is enabled otherwise 0
     * @throws \PDOException
     *  Throw PDOException if the query fails
     */
    public function isTwoFAEnabled($username)
    {
        $data = $this->get($username);
        return (int) $data->otp_status;
    }

    /**
     * Get the secret key from the database
     *
     * @param mixed $username
     *  The username you want to get the secret key
     * @return mixed
     *  Return the secret key from the database
     * @throws \PDOException
     *  Throw PDOException if the query fails
     */
    public function getSecret($username)
    {
        $data = $this->get($username);
        return $data->otp_secret;
    }

    /**
     * Regenrate the session id and set the OTP session to true
     *
     * @return void
     *  Redirect the user to the index page
     */
    public function regenerateSession()
    {
        session_regenerate_id();
        $_SESSION['OTP'] = true;
        $this->utils->redirect($this->utils->siteUrl("/index.php"));
    }
}
