<?php

namespace Uploady;

/**
 * A class that handles Uploady Custom Pages
 *
 * @package Uploady
 * @version 1.5.3
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
     * Page class constructor
     **/
    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Get a page from the database
     *
     * @param string $slug
     *  The page slug
     * @return array|bool
     */
    public function get($slug)
    {
        $this->db->prepare("SELECT * FROM pages WHERE slug = :slug");

        $this->db->bind(":slug", $slug, \PDO::PARAM_STR);

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
    public function update($slug, $title, $content)
    {
        $this->db->prepare("UPDATE pages SET title = :title, content = :content WHERE slug = :slug");

        $this->db->bind(":title", $title, \PDO::PARAM_STR);
        $this->db->bind(":content", $content, \PDO::PARAM_STR);
        $this->db->bind(":slug", $slug, \PDO::PARAM_STR);

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
    public function add($slug, $title, $content)
    {
        $this->db->prepare("INSERT INTO pages (slug, title, content) VALUES (:slug, :title, :content)");

        $this->db->bind(":slug", $slug, \PDO::PARAM_STR);
        $this->db->bind(":title", $title, \PDO::PARAM_STR);
        $this->db->bind(":content", $content, \PDO::PARAM_STR);

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
        $this->db->prepare("DELETE * FROM pages WHERE slug = :slug");

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
    }
}
