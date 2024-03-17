<?php

namespace Uploady;

/**
 * A class that handles software settings
 *
 * @package Uploady
 * @version 3.0.x
 * @author fariscode <farisksa79@gmail.com>
 * @license MIT
 * @link https://github.com/farisc0de/Uploady
 */

class Settings
{
    /**
     * Database Connection
     *
     * @var Database
     */
    private $db;

    /**
     * Settings class constructor
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
     * Get the website settings
     *
     * @return array
     *  Return an array that contains the website settings
     */
    public function getSettings()
    {
        $this->db->prepare("SELECT setting_key,setting_value FROM settings");

        if ($this->db->execute()) {
            $settings = $this->db->resultset();
            $settings_array = array();

            foreach ($settings as $setting) {
                $settings_array[$setting->setting_key] = $setting->setting_value;
            }

            return $settings_array;
        }

        return [];
    }

    /**
     * Update the website settings
     *
     * @param array $settings_array
     *  An array that contains the wesbite settings
     * @return bool
     *  Return true if the settings are updated otherwise false
     */
    public function updateSettings($settings_array)
    {
        $res = [];

        foreach ($settings_array as $setting_key => $setting_value) {
            $this->db->prepare("UPDATE settings SET setting_value = :value WHERE setting_key = :key");

            $this->db->bind(":key", $setting_key, \PDO::PARAM_STR);
            $this->db->bind(":value", $setting_value, \PDO::PARAM_STR);

            array_push($res, $this->db->execute());
        }

        return in_array(false, $res) ? false : true;
    }

    /**
     * Return the value of a setting from the database
     *
     * @param string $setting_key
     *  The key you want to retrive is value from the database
     * @return string
     */
    public function getSettingValue($setting_key)
    {
        $sql = "SELECT setting_value FROM settings WHERE setting_key = :key";

        $this->db->prepare($sql);

        $this->db->bind(":key", $setting_key);

        $setting_value = "";

        if ($this->db->execute()) {

            $result = $this->db->single();

            if ($result) {
                $setting_value = $result->setting_value;
            }
        }

        return $setting_value;
    }

    /**
     * Return an array contains the settings that match the pattern
     *
     * @param string $pattern
     *  The pattern you want to match the settings with
     * @return array
     *  Return an array contains the settings that match the pattern
     */
    public function getSettingsWithPattern($pattern)
    {
        $settings_array = array();

        $sql = "SELECT * FROM settings WHERE setting_key LIKE (:pattern)";

        $this->db->prepare($sql);

        $this->db->bind(":pattern", $pattern);

        if ($this->db->execute()) {
            $settings = $this->db->resultset();

            foreach ($settings as $setting) {
                $settings_array[$setting->setting_key] = $setting->setting_value;
            }
        }

        return $settings_array;
    }

    /**
     * Function that returns an array contains the predefined security questions
     *
     * @return array
     *  Return an array contains the predefined security questions
     */
    public function getPreDefinedQuestions()
    {
        return [
            "1" => "Select a Security Question",
            "2" => "What was your childhood nickname?",
            "3" => "What is the name of your favorite childhood friend?",
            "4" => "What was the name of your first stuffed animal?",
            "5" => "Where were you when you had your first kiss?",
            "6" => "What is the name of the company of your first job?",
            "7" => "What was your favorite place to visit as a child?",
            "8" => "What was your dream job as a child?",
            "9" => "What is your preferred musical genre?",
            "10" => "What is your favorite team?",
            "11" => "What is your father\"s middle name?"
        ];
    }
}
