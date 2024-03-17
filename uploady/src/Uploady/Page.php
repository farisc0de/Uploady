<?php

namespace Uploady;

/**
 * A class that handles Uploady Custom Pages
 *
 * @package Uploady
 * @version 3.0.x
 * @author fariscode <farisksa79@gmail.com>
 * @license MIT
 * @link https://github.com/farisc0de/Uploady
 */
class Page
{
    /**
     * Database Connection
     *
     * @var Database
     */
    private $db;

    /**
     * Localization
     *
     * @var Localization
     */
    private $localization;

    /**
     * Page class constructor
     **/
    public function __construct($db, $localization)
    {
        $this->db = $db;
        $this->localization = $localization;
    }

    /**
     * Get a page from the database
     *
     * @param string $slug
     *  The page slug
     * @return object|bool
     */
    public function get($slug, $language)
    {
        $this->db->prepare("SELECT id FROM pages WHERE slug = :slug");

        $this->db->bind(":slug", $slug, \PDO::PARAM_STR);

        $this->db->execute();

        $page = $this->db->single();

        $this->db->prepare("SELECT id FROM languages WHERE language_code = :code");

        $this->db->bind(":code", $language, \PDO::PARAM_STR);

        $this->db->execute();

        $language = $this->db->single();

        $this->db->prepare("SELECT * FROM 
         pages_translation WHERE
         page_id = :page_id AND language_id = :language_id");

        $this->db->bind(":page_id", $page->id, \PDO::PARAM_INT);
        $this->db->bind(":language_id", $language->id, \PDO::PARAM_INT);

        $this->db->execute();

        return $this->db->single();
    }

    /**
     * Update a page in the database
     *
     * @param string $slug
     *  The page slug
     * @return bool
     */
    public function update($id, $new_slug)
    {
        $this->db->prepare("UPDATE pages SET slug = :new_slug WHERE id = :id");

        $this->db->bind(":id", $id, \PDO::PARAM_INT);
        $this->db->bind(":new_slug", $new_slug, \PDO::PARAM_STR);

        return $this->db->execute();
    }

    /**
     * Get all pages from the database
     *
     * @return array
     */
    public function getAll()
    {
        $this->db->prepare("SELECT * FROM pages");

        $this->db->execute();

        return $this->db->resultset();
    }

    /**
     * Add a new page to the database
     *
     * @return bool
     */
    public function add($slug)
    {
        $this->db->prepare("INSERT INTO pages (slug, deletable) VALUES (:slug, :is_deletable)");

        $this->db->bind(":slug", $slug, \PDO::PARAM_STR);
        $this->db->bind(":is_deletable", true, \PDO::PARAM_BOOL);

        return $this->db->execute();
    }

    /**
     * Delete a page from the database
     *
     * @param string $slug
     *  The page slug
     * @return bool
     */
    public function delete($slug)
    {
        $this->db->prepare("SELECT deletable FROM pages WHERE slug = :slug");

        $this->db->bind(":slug", $slug, \PDO::PARAM_STR);

        $this->db->execute();

        $page = $this->db->single();

        if (!$page->deletable) {
            return false;
        }

        $this->db->prepare("DELETE FROM pages WHERE slug = :slug");

        $this->db->bind(":slug", $slug, \PDO::PARAM_STR);

        return $this->db->execute();
    }

    /**
     * Check if a page exists in the database
     *
     * @param string $slug
     *  The page slug
     * @return bool
     */
    public function isExist($slug)
    {
        $this->db->prepare("SELECT * FROM pages WHERE slug = :slug;");

        $this->db->bind(":slug", $slug, \PDO::PARAM_STR);

        if ($this->db->execute()) {
            return $this->db->rowCount() ? true : false;
        }

        return false;
    }

    /**
     * Count all pages in the database
     *
     * @return int
     */
    public function countAll()
    {
        $this->db->prepare("SELECT * FROM pages");

        $this->db->execute();

        return $this->db->rowCount();
    }

    /**
     * Get the page slug
     *
     * @param int $id
     *  The page id
     * @return object
     */
    public function getSlug($id)
    {
        $this->db->prepare("SELECT slug FROM pages WHERE id = :id");

        $this->db->bind(":id", $id, \PDO::PARAM_INT);

        $this->db->execute();

        return $this->db->single();
    }
}
