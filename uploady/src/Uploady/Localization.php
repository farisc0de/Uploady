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

    private string $languagesDir;

    /**
     * The constructor
     *
     * @param Database $db
     *  The database connection
     */
    public function __construct($db)
    {
        $this->db = $db;
        $this->languagesDir = realpath(APP_PATH . '/languages');
    }

    /**
     * Validate and sanitize language code to prevent path traversal
     *
     * @param string $language The language code
     * @return string|null Sanitized language code or null if invalid
     */
    private function validateLanguageCode(string $language): ?string
    {
        $sanitized = preg_replace('/[^a-zA-Z0-9_-]/', '', $language);
        
        if (empty($sanitized) || strlen($sanitized) > 10) {
            return null;
        }
        
        return $sanitized;
    }

    /**
     * Get safe path to language file with path traversal protection
     *
     * @param string $language The language code
     * @return string|null Safe file path or null if invalid
     */
    private function getLanguageFilePath(string $language): ?string
    {
        $sanitized = $this->validateLanguageCode($language);
        if ($sanitized === null) {
            return null;
        }

        $filePath = $this->languagesDir . '/' . $sanitized . '.json';
        $realPath = realpath($filePath);

        if ($realPath === false) {
            return $this->languagesDir . '/' . $sanitized . '.json';
        }

        if (!str_starts_with($realPath, $this->languagesDir)) {
            return null;
        }

        return $realPath;
    }

    /**
     * Function to load language file in array
     *
     * @param mixed $language
     *  The language file name
     * @return mixed
     *  An array contains the language file data
     */
    public function loadLangauge(string $language): ?array
    {
        $filePath = $this->getLanguageFilePath($language);
        
        if ($filePath === null || !file_exists($filePath)) {
            return null;
        }

        $content = file_get_contents($filePath);
        if ($content === false) {
            return null;
        }

        $data = json_decode($content, true);
        return is_array($data) ? $data : null;
    }

    /**
     * Function to create a new language file
     *
     * @param mixed $language
     *  The language file name
     * @return void
     *  Create a new language file
     */
    public function createLanguage(string $language): bool
    {
        $sanitized = $this->validateLanguageCode($language);
        if ($sanitized === null) {
            return false;
        }

        $templatePath = $this->getLanguageFilePath('en');
        if ($templatePath === null || !file_exists($templatePath)) {
            return false;
        }

        $targetPath = $this->languagesDir . '/' . $sanitized . '.json';
        
        if (!str_starts_with(realpath(dirname($targetPath)) ?: '', $this->languagesDir)) {
            return false;
        }

        $content = file_get_contents($templatePath);
        if ($content === false) {
            return false;
        }

        $data = json_decode($content, true);
        if (!is_array($data)) {
            return false;
        }

        return file_put_contents($targetPath, json_encode($data, JSON_PRETTY_PRINT)) !== false;
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
    public function updateLanguage(string $type, array $data, string $language): bool
    {
        $filePath = $this->getLanguageFilePath($language);
        
        if ($filePath === null || !file_exists($filePath)) {
            return false;
        }

        $content = file_get_contents($filePath);
        if ($content === false) {
            return false;
        }

        $file = json_decode($content, true);
        if (!is_array($file)) {
            return false;
        }

        $type = preg_replace('/[^a-zA-Z0-9_]/', '', $type);
        
        foreach ($data as $key => $value) {
            $safeKey = preg_replace('/[^a-zA-Z0-9_]/', '', $key);
            $file[$type][$safeKey] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        }

        return file_put_contents($filePath, json_encode($file, JSON_PRETTY_PRINT)) !== false;
    }

    /**
     * Function to delete language file
     *
     * @param mixed $language
     *  The language file name
     * @return void
     *  Delete the language file
     */
    public function deleteLanguage(string $language): bool
    {
        if ($language === 'en') {
            return false;
        }

        $filePath = $this->getLanguageFilePath($language);
        
        if ($filePath === null || !file_exists($filePath)) {
            return false;
        }

        return unlink($filePath);
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
