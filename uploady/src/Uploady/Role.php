<?php

namespace Uploady;

class Role
{
    /**
     * Database
     *
     * @var Database
     */
    private $db;

    /**
     * User
     *
     * @var User
     */
    private $user;

    /**
     * Role class constructor
     **/
    public function __construct($db, $user)
    {
        $this->db = $db;
        $this->user = $user;
    }

    /**
     * Get all roles
     *
     * @return array|bool
     */
    public function getAll()
    {
        $this->db->prepare("SELECT * FROM roles");

        $this->db->execute();

        return $this->db->resultset();
    }

    /**
     * Get a role
     *
     * @param string $role
     *  The role name
     * @return array|bool
     */

    public function get($role)
    {
        $this->db->prepare("SELECT * FROM roles WHERE name = :role");

        $this->db->bind(":role", $role, \PDO::PARAM_STR);

        $this->db->execute();

        return $this->db->single();
    }

    /**
     * Get the role of a user
     *
     * @param string $username
     *  The username
     * @return array|bool
     */
    public function getUserRole($username)
    {
        $user = $this->user->get($username);

        if ($user) {
            $role = $this->get($user->role);

            if ($role) {
                return $role;
            }
        }

        return false;
    }

    /**
     * Create a new role
     *
     * @param mixed $role
     *  The role name
     * @return bool
     *  No return value
     */
    public function createRole($role)
    {
        $this->db->prepare("INSERT INTO roles (name) VALUES (:role)");

        $this->db->bind(":role", $role, \PDO::PARAM_STR);

        return $this->db->execute();
    }

    /**
     * Update a role
     *
     * @param mixed $role
     *  The role name
     * @param mixed $id
     *  The role id
     * @return void
     *  No return value
     */
    public function updateRole($role, $id)
    {
        $this->db->prepare("UPDATE roles SET name = :role WHERE id = :id");

        $this->db->bind(":role", $role, \PDO::PARAM_STR);
        $this->db->bind(":id", $id, \PDO::PARAM_INT);

        return $this->db->execute();
    }

    /**
     * Delete a role
     *
     * @param mixed $id
     *  The role id
     * @return void
     *  No return value
     */
    public function deleteRole($id)
    {
        $this->db->prepare("DELETE FROM roles WHERE id = :id");

        $this->db->bind(":id", $id, \PDO::PARAM_INT);

        $this->db->execute();
    }
}
