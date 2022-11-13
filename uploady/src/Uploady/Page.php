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

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function get($slug)
    {
        $this->db->query("SELECT * FROM pages WHERE slug = :slug");

        $this->db->bind(":slug", $slug, \PDO::PARAM_STR);

        $this->db->execute();

        return $this->db->single();
    }

    public function update($slug)
    {
        # code...
    }

    public function getAll()
    {
        $this->db->query("SELECT * FROM pages");

        $this->db->execute();

        return $this->db->resultset();
    }

    public function add()
    {
        # code...
    }

    public function delete($slug)
    {
        $this->db->query("DELETE * FROM pages WHERE slug = :slug");

        $this->db->bind(":slug", $slug, \PDO::PARAM_STR);

        return $this->db->execute();
    }

    public function isExist($slug)
    {
        $this->db->query("SELECT * FROM pages WHERE slug = :slug;");

        $this->db->bind(":slug", $slug, \PDO::PARAM_STR);

        if ($this->db->execute()) {
            return $this->db->rowCount() ? true : false;
        }
    }
}
