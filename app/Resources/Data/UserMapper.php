<?php

namespace App\Resources\Data;

use App\Resources\Models\User;

class UserMapper extends BaseMapper {
    /**
     * The database table used by the model
     * 
     * @val string
     */
    protected $TABLE = "users";

    public function __construct()
    {
        parent::__construct();
        $this->setUpTable();
    }

    private function setUpTable() : bool
    {
        $fields = ["id TEXT PRIMARY KEY", "username TEXT UNIQUE", "password TEXT"];
        $q = parent::getCreateTableQuery($this->TABLE, $fields);
        $resp = $this->db->statement($q);
        return $resp;
    }

    public function save(User $user) : bool
    {
        $fields = ["id", "username", "password"];
        $values = [$user->getIDString(), $user->getUsernameString(), $user->getPasswordString()];

        $q = parent::getUpsertQuery($this->TABLE, $fields, $values);
        $resp = $this->db->insert($q); 
        return $resp;
    }

    public function find(string $id) : ?User
    {
        $q = parent::getSelectQuery($this->TABLE, ["*"], ["id='{$id}'"]);
        $resp = parent::_find($q);
        return $this->load($resp);
    }

    public function findByUsername(string $username) : ?User
    {
        $q = parent::getSelectQuery($this->TABLE, ["*"], ["username='{$username}'"]);
        $resp = parent::_find($q);
        return $this->load($resp);
    }


    public function delete(User $user) : bool
    {
        $q = parent::getDeleteQuery($this->TABLE, ["id='{$user->getIDString()}'"]);
        $resp = $this->db->delete($q);
        return $resp;
    }

    protected function _load(object $dump) : ?User
    {
        if (!$dump){
            return null;
        }

        return new User($dump->username, $dump->password, $dump->id);
    }

    public function userExists(string $username) : bool
    {
        $q = parent::getSelectQuery($this->TABLE, ["*"], ["username='{$username}'"]);
        $resp = parent::_find($q);
        return $resp != null;
    }

}















?>