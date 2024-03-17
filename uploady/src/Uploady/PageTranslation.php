<?php

namespace Uploady;

/**
 * A class that handles Page Translation
 *
 * @package Uploady
 * @version 3.0.x
 * @author fariscode <farisksa79@protonmail.com>
 * @license MIT
 * @link https://github.com/farisc0de/Uploady
 */

class PageTranslation
{
    /**
     * Database connection
     *
     * @var Database
     */
    private $db;

    /**
     * Class constructor
     *
     * @param Database $db
     */
    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Add a new translation record
     *
     * @param array $data
     *  The data to be inserted
     * @return mixed
     */
    public function addTranslation($data)
    {
        $this->db->prepare("INSERT INTO pages_translation (page_id, language_id, title, content) VALUES (:page, :language, :title, :content)");

        $this->db->bind(":language", $data['lang_id']);

        $this->db->bind(":page", $data['page_id']);

        $this->db->bind(":title", $data['title']);

        $this->db->bind(":content", $data['content']);

        return $this->db->execute();
    }

    /**
     * Update a translation record
     *
     * @param array $data
     *  The data to be updated
     * @return mixed
     */
    public function updateTranslation($data)
    {
        $this->db->prepare("UPDATE pages_translation SET
         title = :title,
         content = :content WHERE 
         language_id = :language AND page_id = :page");

        $this->db->bind(":language", $data['lang_id']);

        $this->db->bind(":page", $data['page_id']);

        $this->db->bind(":title", $data['title']);

        $this->db->bind(":content", $data['content']);

        return $this->db->execute();
    }

    /**
     * Get a translation
     *
     * @param mixed $language
     *  The language id
     * @param mixed $page
     *  The page id
     * @return mixed
     *  Returns the translation if found, false otherwise
     */
    public function getTranslation($language, $page)
    {
        $this->db->prepare("SELECT * FROM pages_translation WHERE language_id = :language AND page = :page");
        $this->db->bind(":language", $language);
        $this->db->bind(":page", $page);
        $this->db->execute();
        return $this->db->single();
    }

    /**
     * Get all translation records
     * @return mixed
     */
    public function getTranslations()
    {
        $this->db->prepare("SELECT * FROM pages_translation");

        $this->db->execute();

        return $this->db->resultset();
    }

    /**
     * Get a translation record
     * @param mixed $id
     * The id of the translation record
     * @return mixed
     * Returns the translation record if found, false otherwise
     */
    public function getTranslationRecord($id)
    {
        $this->db->prepare("SELECT * FROM pages_translation WHERE id = :id");
        $this->db->bind(":id", $id);
        $this->db->execute();
        return $this->db->single();
    }

    /**
     * Delete a translation record
     * 
     * @param int $id
     *  The id of the translation record
     * @return bool
     *  Returns true if the record is deleted, false otherwise
     */
    public function deleteTranslation($id)
    {
        $this->db->prepare("DELETE FROM pages_translation WHERE id = :id");
        $this->db->bind(":id", $id);
        return $this->db->execute();
    }
}
