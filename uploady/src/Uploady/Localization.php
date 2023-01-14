<?php

namespace Uploady;

/**
 * Simple Class that handles localization
 *
 * @package Uploady
 * @version 1.5.3
 * @author fariscode <farisksa79@protonmail.com>
 * @license MIT
 * @link https://github.com/farisc0de/Uploady
 */
class Localization
{

    /**
     * The database connection
     *
     * @var Database
     */

    private $db;

    /**
     * The constructor
     *
     * @param Database $db
     *  The database connection
     */
    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Function to load language file in array
     *
     * @param mixed $language
     *  The language file name
     * @return mixed 
     *  An array contains the language file data
     */
    public function loadLangauge($language)
    {
        $file = file_get_contents(APP_PATH . "/languages/{$language}.json");
        $file = json_decode($file, true);
        return $file;
    }

    /**
     * Function to create a new language file
     *
     * @param mixed $language
     *  The language file name
     * @return void 
     *  Create a new language file
     */
    public function createLanguage($language)
    {
        $file = file_get_contents(APP_PATH . "/languages/en.json");
        $file = json_decode($file, true);
        $file = json_encode($file, JSON_PRETTY_PRINT);
        file_put_contents(APP_PATH . "/languages/{$language}.json", $file);
    }

    /**
     * Function to update language file
     *
     * @param mixed $data
     *  An array contains the new data
     * @param mixed $language
     *  The language file name
     * @return void 
     *  Update the language file
     */
    public function updateLanguage($data, $language)
    {
        $file = file_get_contents(APP_PATH . "/languages/{$language}.json");
        $file = json_decode($file, true);

        foreach ($data as $key => $value) {
            $file[$key] = $value;
        }

        $file = json_encode($file, JSON_PRETTY_PRINT);
        file_put_contents(APP_PATH . "/languages/{$language}.json", $file);
    }

    /**
     * Function to delete language file
     *
     * @param mixed $language
     *  The language file name
     * @return void 
     *  Delete the language file
     */
    public function deleteLanguage($language)
    {
        unlink(APP_PATH . "/languages/{$language}.json");
    }

    /**
     * Function to change the current language
     *
     * @return mixed 
     *  The current language
     */
    public function setLanguage($language)
    {
        $_SESSION['language'] = $language;
    }

    /**
     * Function to get the current language
     *
     * @return mixed 
     *  The current language
     */
    public function getLanguage()
    {
        if (isset($_SESSION['language'])) {
            return $_SESSION['language'];
        } else {
            return "en";
        }
    }

    public function getLanguages()
    {
        $languages = "SELECT * FROM languages";

        $this->db->prepare($languages);

        $this->db->execute();

        return $this->db->resultset();
    }
}
