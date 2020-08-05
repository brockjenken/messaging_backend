<?php

namespace App\Resources\Data;

use App\Resources\Models\User;

class UserMapper extends BaseMapper {

    /**
     * Holds the name of the database table
     * 
     * @val string
     */
    protected $TABLE = "users";

    public function __construct()
    {
        parent::__construct();
        $this->setUpTable();
    }

    /**
     * Creates table for Users and returns True on success
     *
     * @return boolean
     */
    private function setUpTable() : bool
    {
        $fields = ["id TEXT PRIMARY KEY", "username TEXT UNIQUE", "password TEXT"];
        $q = parent::getCreateTableQuery($this->TABLE, $fields);
        $resp = $this->db->statement($q);
        return $resp;
    }

    /**
     * Saves a User to the database and returns True on success
     *
     * @param User $user
     * @return boolean
     */
    public function save(User $user) : bool
    {
        $fields = ["id", "username", "password"];
        $values = $this->dump($user);

        $q = parent::getUpsertQuery($this->TABLE, $fields, $values);
        $resp = $this->db->insert($q); 
        return $resp;
    }


    /**
     * Finds a User in database via it's ID. Returns null if not found
     * 
     * @param string $id
     * @return User|null
     */
    public function find(string $id) : ?User
    {
        $q = parent::getSelectQuery($this->TABLE, ["*"], ["id='$id'"]);
        $resp = parent::_find($q);
        return $this->load($resp);
    }

    /**
     * Finds a User in database via it's username. Returns null if not found
     * 
     * @param string $id
     * @return User|null
     */
    public function findByUsername(string $username) : ?User
    {
        $q = parent::getSelectQuery($this->TABLE, ["*"], ["username='$username'"]);
        $resp = parent::_find($q);
        return $this->load($resp);
    }

    /**
     * Deletes a User from the database. Returns True on success
     *
     * @param User $user
     * @return boolean
     */
    public function delete(User $user) : bool
    {
        $q = parent::getDeleteQuery($this->TABLE, ["id='{$user->getIDString()}'"]);
        $resp = $this->db->delete($q);
        return $resp;
    }

    /**
     * Used to determine if a username already exists in the database.
     * 
     * This method doesn't load the User object, making it faster than calling find()
     *
     * @param string $username
     * @return boolean
     */
    public function usernameExists(string $username) : bool
    {
        $q = parent::getSelectQuery($this->TABLE, ["*"], ["username='$username'"]);
        $resp = parent::_find($q);

        return (bool) $resp;
    }

    /**
     * Will load a User object from the database dump. Returns null if object is empty
     *
     * @param object $dump
     * @return User|null
     */
    protected function _load(object $dump) : ?User
    {
        if (!$dump){
            return null;
        }

        return new User($dump->username, $dump->password, $dump->id);
    }

    /**
     * Converts User object into an array for saving
     *
     * @param User $user
     * @return varray
     */
    private function dump(User $user) : array
    {
        return [
            $user->getIDString(), 
            $user->getUsernameString(), 
            $user->getPasswordString()
        ];
    }



}















?>