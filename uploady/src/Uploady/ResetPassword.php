<?php

namespace Uploady;

/**
 * A class that handles Reset Password Requsts
 *
 * @package Uploady
 * @version 3.0.x
 * @author fariscode <farisksa79@gmail.com>
 * @license MIT
 * @link https://github.com/farisc0de/Uploady
 */
class ResetPassword
{
    /**
     * Database Connection
     *
     * @var Database
     */
    private $db;

    /**
     * User Class
     *
     * @var User
     */
    private $user;

    /**
     * User Class
     *
     * @var Utils
     */
    private $utils;

    /**
     * Template Class
     *
     * @var Template
     */
    private $tpl;

    /**
     * ResetPassword class constructor
     *
     * @param object $database
     *  An object from the Database class
     * @param object $user
     *  An object from the User class
     * @param object $utils
     *  An object from the Utils class
     * @param object $tpl
     *  An object from the Template class
     * @return void
     */
    public function __construct($database, $user, $utils, $tpl)
    {
        $this->db = $database;

        $this->user = $user;

        $this->utils = $utils;

        $this->tpl = $tpl;
    }

    /**
     * Generate a secure token
     *
     * @return string
     *  Return a secure token
     */
    public function generateToken()
    {
        return bin2hex(random_bytes(16));
    }

    /**
     * Send an email to the user with the reset password link
     *
     * @param string $username
     *  The username you want to send a reset password message to
     * @return bool return
     *  True if the message is send otherwise false
     */
    public function sendMessage($username)
    {
        $sendmail = new Mailer($this->db);

        if (!$this->user->isExist($username)) {
            return false;
        }

        $token = $this->generateToken();

        $rows = $this->user->get($username);

        $email = $rows->email;

        $created_at = date("Y-m-d h:i:s", time());

        $this->db->prepare("UPDATE users SET
             reset_hash = :hash,
             created_at = :ct
             WHERE username = :user");

        $this->db->bind(":user", $rows->username, \PDO::PARAM_STR);
        $this->db->bind(":hash", hash("sha256", $token), \PDO::PARAM_STR);
        $this->db->bind(":ct", $created_at, \PDO::PARAM_STR);

        if ($this->db->execute()) {
            $sendmail->sendMessage(
                $email,
                "Reset password instructions",
                $this->tpl->loadTemplate("forget_password_email", [
                    'username' => $rows->username,
                    'actual_link' => $this->utils->siteUrl(),
                    'token' => $token
                ])
            );
        }

        return true;
    }

    /**
     * Update the user password
     *
     * @param string $key
     *  The token to verfiy the request
     * @param string $username
     *  The username you want to update it password
     * @param string $password
     *  The new password that the user chooses
     * @return bool
     *  True if the password is updated otherwise false
     */
    public function updatePassword($key, $username, $password)
    {
        $validations = [
            "uppercase" => preg_match('@[A-Z]@', $password),
            "lowercase" => preg_match('@[a-z]@', $password),
            "number" => preg_match('@[0-9]@', $password),
            "specialChars" => preg_match('@[^\w]@', $password),
            "length" => strlen($password) >= 8
        ];

        if (in_array(false, $validations, true)) {
            return false;
        }

        $this->db->prepare("UPDATE users SET password = :password WHERE username = :username");

        $this->db->bind(":password", password_hash($password, PASSWORD_BCRYPT), \PDO::PARAM_STR);
        $this->db->bind(":username", $username, \PDO::PARAM_STR);

        if ($this->db->execute()) {
            return $this->deleteToken($key);
        }
    }

    /**
     * Get the username using the sha256 token
     *
     * @param string $token
     *  The secure token to verfiy the request
     * @return object|bool
     *  An object contains the username or false
     */
    public function getUserAssignedToToken($token)
    {
        $this->db->prepare("SELECT username FROM users WHERE reset_hash = :token limit 1");

        $this->db->bind(":token", hash("sha256", $token), \PDO::PARAM_STR);

        return $this->db->execute() ? $this->db->single() : false;
    }

    /**
     * Delete the token from the database
     *
     * @param string $token
     *  The secure token you want to delete
     * @return bool
     *  Return true if the token is deleted otherwise false
     */
    public function deleteToken($token)
    {
        $this->db->prepare(
            "UPDATE users SET
         reset_hash = :null,
         created_at = :nullct WHERE
         reset_hash = :token"
        );

        $this->db->bind(":token", hash("sha256", $token), \PDO::PARAM_STR);
        $this->db->bind(":null", null, \PDO::PARAM_NULL);
        $this->db->bind(":nullct", null, \PDO::PARAM_NULL);

        return $this->db->execute();
    }

    /**
     * Check if the token exists or not
     *
     * @param string $token
     *  The secure token you want to check
     * @return bool
     *  Return true if the token exists otherwise false
     */
    public function isExist($token)
    {
        $this->db->prepare("SELECT * FROM users WHERE reset_hash = :token");

        $this->db->bind(":token", hash("sha256", $token), \PDO::PARAM_STR);

        if ($this->db->execute()) {
            if ($this->db->rowCount()) {
                return $this->isExpired($token) ? false : true;
            }
        }

        return false;
    }

    /**
     * Check if the token is expired or not
     *
     * @param string $key
     *  The secure token you want to check
     * @return bool
     *  Return true if the token expired otherwise false
     */
    public function isExpired($key)
    {
        $this->db->prepare("SELECT created_at FROM users WHERE reset_hash = :token");

        $this->db->bind(":token", hash("sha256", $key), \PDO::PARAM_STR);

        if ($this->db->execute()) {
            $data = $this->db->single();

            $diff = time() - strtotime($data->created_at);

            if (round($diff / 3600) >= 24) {
                $this->deleteToken($key);
                return true;
            }

            return false;
        }

        return false;
    }
}
