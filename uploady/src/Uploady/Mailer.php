<?php

namespace Uploady;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Simple Class that handles emails created using PHPMailer
 *
 * @package Uploady
 * @version 1.5.3
 * @author fariscode <farisksa79@gmail.com>
 * @license MIT
 * @link https://github.com/farisc0de/Uploady
 */
class Mailer
{
    /**
     * Database Connection
     *
     * @var Database
     */
    private $db;

    /**
     * Mailer class constructor
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
     * Get settings using pattern to use with PHPMailer
     *
     * @return array
     *  Return an array containing the settings
     */
    public function getSettingWithPattern($pattern)
    {
        $this->db->query(
            "SELECT setting_key,setting_value FROM settings WHERE setting_key LIKE :like"
        );

        $this->db->bind(":like", $pattern);

        if ($this->db->execute()) {
            $settings = $this->db->resultset();
            $settings_array = array();
            foreach ($settings as $setting) {
                $settings_array[$setting->setting_key] = $setting->setting_value;
            }
            return $settings_array;
        }
    }

    /**
     * Check if SMTP is enabled then use PHPMailer to send a message
     *
     * @param string $email
     *  The email you want to send a message to
     * @param string $subject
     *  The message subject
     * @param string $body
     *  The message body plain or HTML
     * @return bool
     *  Return true if the message is sent or false otherwise
     */
    public function sendMessage($email, $subject, $body)
    {
        try {
            $smtp = $this->getSettingWithPattern('smtp_%');
            $owner = $this->getSettingWithPattern('owner_%');

            $mail = new PHPMailer(true);

            if ($smtp['smtp_status'] == true) {
                $mail->isSMTP();

                $mail->Host = $smtp['smtp_security'] . "://" . $smtp['smtp_host'] . ":" . $smtp['smtp_port'];
                $mail->SMTPAuth = true;
                $mail->Username = $smtp['smtp_username'];
                $mail->Password = $smtp['smtp_password'];
            }

            $mail->setFrom($owner['owner_email'], $owner['owner_name']);

            $mail->addAddress($email);

            $mail->isHTML(true);

            $mail->Subject = $subject;

            $mail->Body = $body;

            return $mail->send();
        } catch (Exception $th) {
            echo $th->errorMessage();
        }
    }
}
