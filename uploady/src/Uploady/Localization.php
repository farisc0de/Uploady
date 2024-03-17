<?php

namespace Uploady;

use PDOException;

/**
 * Simple Class that handles localization
 *
 * @package Uploady
 * @version 3.0.x
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
        $file = file_get_contents(realpath(APP_PATH . "/languages/{$language}.json"));
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
        $file = file_get_contents(realpath(APP_PATH . "/languages/en.json"));
        $file = json_decode($file, true);
        $file = json_encode($file, JSON_PRETTY_PRINT);
        file_put_contents(realpath(APP_PATH . "/languages/{$language}.json"), $file);
    }

    /**
     * Functopm to update language direction
     * @param mixed $language
     *  The language code
     * @param mixed $direction
     *  The language direction
     * @return bool
     *  True if the language direction updated successfully, false otherwise
     * @throws PDOException
     *  If the query failed to execute
     */
    public function updateLanguageDirection($language, $direction)
    {
        $query = "UPDATE languages SET language_direction = :direction WHERE language_code = :code";

        $this->db->prepare($query);

        $this->db->bind(":direction", $direction, \PDO::PARAM_STR);

        $this->db->bind(":code", $language, \PDO::PARAM_STR);

        return $this->db->execute();
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
    public function updateLanguage($type, $data, $language)
    {
        $file = file_get_contents(realpath(APP_PATH . "/languages/{$language}.json"));
        $file = json_decode($file, true);

        foreach ($data as $key => $value) {
            $file[$type][$key] = $value;
        }

        $file = json_encode($file, JSON_PRETTY_PRINT);
        file_put_contents(realpath(APP_PATH . "/languages/{$language}.json"), $file);
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
        unlink(realpath(APP_PATH . "/languages/{$language}.json"));
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

    /**
     * Function to get all active languages
     *
     * @return mixed
     *  An array contains all active languages
     */
    public function getActiveLanguages()
    {
        $languages = "SELECT * FROM languages WHERE is_active = 1";

        $this->db->prepare($languages);

        $this->db->execute();

        return $this->db->resultset();
    }

    /**
     * Function to get all languages
     *
     * @return mixed
     *  An array contains all languages
     */
    public function getLanguages()
    {
        $languages = "SELECT * FROM languages";

        $this->db->prepare($languages);

        $this->db->execute();

        return $this->db->resultset();
    }

    /**
     * Function to get language by code
     *
     * @param mixed $code
     *  The language code
     * @return mixed
     *  An array contains the language data
     */
    public function getLanguageByCode($code)
    {
        $language = "SELECT * FROM languages WHERE language_code = :code";

        $this->db->prepare($language);

        $this->db->bind(":code", $code);

        $this->db->execute();

        return $this->db->single();
    }

    /**
     * Function to add new language
     *
     * @param mixed $code
     *  The language code
     * @return mixed
     *  True if the language added successfully, false otherwise
     */
    public function activateLanguage($code)
    {
        $language = "UPDATE languages SET is_active = 1 WHERE language_code = :code";

        $this->db->prepare($language);

        $this->db->bind(":code", $code);

        $this->db->execute();

        if (!file_exists(APP_PATH . "/languages/{$code}.json")) {
            $this->createLanguage($code);
        }

        return true;
    }

    /**
     * Function to deactivate language
     *
     * @param mixed $code
     *  The language code
     * @return mixed
     *  True if the language deactivated successfully, false otherwise
     */

    public function deactivateLanguage($code)
    {
        $language = "UPDATE languages SET is_active = 0 WHERE language_code = :code";

        $this->db->prepare($language);

        $this->db->bind(":code", $code);

        return $this->db->execute();
    }
}
