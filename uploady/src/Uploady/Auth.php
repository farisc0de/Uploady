<?php

namespace Uploady;

/**
 * Class to Handle User Auth
 *
 * @package Uploady
 * @version 3.0.x
 * @author fariscode <farisksa79@gmail.com>
 * @license MIT
 * @link https://github.com/farisc0de/Uploady
 */
class Auth
{
    /**
     * Database Connection
     *
     * @var Database
     */
    private $db;


    /**
     * User connection
     *
     * @var User
     */
    private $user;

    /**
     * Utils Connection
     *
     * @var Utils
     */
    private $utils;

    /**
     * Auth class constructor
     *
     * @param object $database
     *  An object from the Database class
     * @param object $utils
     *  An object from the Utils class
     * @return void
     */
    public function __construct($database, $utils, $user)
    {
        $this->db = $database;

        $this->utils = $utils;

        $this->user = $user;
    }

    /**
     * Generate a device id based on the browser user agent
     *
     * @return string
     *  Returns the user device id
     */
    public function generateDeviceID()
    {
        return hash("sha256", $this->utils->sanitize($_SERVER['HTTP_USER_AGENT']));
    }

    /**
     * Check login information with brute force protection
     *
     * @param mixed $username
     *  A username used for authentication
     * @param mixed $password
     *  A password used for authentication
     * @return int
     *  A status code used to validate the user
     */
    public function newLogin($username, $password)
    {
        $total_failed_login = 5;
        $lockout_time = 15;
        $account_locked = false;

        $this->db->prepare('SELECT * FROM users WHERE username = (:user) LIMIT 1;');

        $this->db->bind(':user', $username, \PDO::PARAM_STR);

        $this->db->execute();

        $row = $this->db->single();

        if (($this->db->rowCount() >= 1) && ($row->failed_login >= $total_failed_login)) {
            $last_login = strtotime($row->last_login);
            $timeout = $last_login + ($lockout_time * 60);
            $timenow = time();

            if ($timenow < $timeout) {
                $account_locked = true;
                return 403;
            }
        }

        if ($row->is_active == 0) {
            return 405;
        }

        if (
            ($this->db->rowCount() == 1) &&
            (password_verify($password, $row->password)) &&
            ($account_locked == false)
        ) {
            $last_login = $row->last_login;

            $this->db->prepare('UPDATE users SET failed_login = 0 WHERE username = (:user) LIMIT 1;');

            $this->db->bind(':user', $username, \PDO::PARAM_STR);

            $this->db->execute();

            return 200;
        } else {
            sleep(random_int(2, 4));

            $this->db->prepare(
                'UPDATE users SET failed_login = (failed_login + 1) WHERE username = (:user) LIMIT 1;'
            );

            $this->db->bind(':user', $username, \PDO::PARAM_STR);

            $this->db->execute();

            return 401;
        }

        $this->db->prepare('UPDATE users SET last_login = now() WHERE username = (:user) LIMIT 1;');

        $this->db->bind(':user', $username, \PDO::PARAM_STR);

        $this->db->execute();
    }


    /**
     * Authenticate the user using the API key
     *
     * @return boolean
     */
    public function authenticateApiKey(): bool
    {
        if (empty($_SERVER['HTTP_X_API_KEY'])) {
            http_response_code(400);
            echo json_encode(["message" => "Missing API key"]);
            return false;
        }

        $api_key = $_SERVER['HTTP_X_API_KEY'];

        $user = $this->user->getByApiKey($api_key);

        if ($user == false) {
            http_response_code(401);
            echo json_encode(["message" => "Invalid API key"]);
            return false;
        }

        $_SESSION['user_id'] = $user->user_id;
        $_SESSION['user_role'] = $user->role;

        return true;
    }

    /**
     * Check the browser unique id to verify the user
     *
     * @param string $uniqueid
     *  The current session unique id to check aginst cookies
     * @return bool
     *  Return true if the token is correct false otherwise
     */
    public function checkDeviceId($uniqueid)
    {
        if (isset($_COOKIE['2fa'])) {
            if (isset($_COOKIE['device_id'])) {
                return ($_COOKIE['2fa'] == $uniqueid);
            }
        }

        return false;
    }

    /**
     * Check CSRF token of the user to authenticate requests
     *
     * @param string $user_token
     *  The user token from the hidden form field
     * @param string $session_token
     *  The CSRF token from the session
     * @return bool
     *  Returns true if the tokens are equal otherwise false
     */
    public function checkToken($user_token, $session_token)
    {
        return (isset($session_token) && $user_token == $session_token);
    }

    /**
     * Generate a new CSRF Token when needed
     *
     * @return void
     */
    public function generateSessionToken($distroyToken = false)
    {
        if ($distroyToken == true) {
            $this->destroySessionToken();
        }

        $_SESSION['csrf'] = hash("sha256", uniqid() . $_SESSION['current_ip'] . session_id());
    }

    /**
     * Delete the old CSRF token when needed
     *
     * @return void
     */
    public function destroySessionToken()
    {
        unset($_SESSION['csrf']);
    }
}
