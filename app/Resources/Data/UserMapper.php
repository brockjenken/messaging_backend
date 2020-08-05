<?php

namespace App\Resources\Data;

use App\Resources\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
     *  Sets up table if not already present
     *
     * @return void
     */
    protected function setUpTable()
    {
        if (!Schema::hasTable($this->TABLE)){
            Schema::create($this->TABLE, function (Blueprint $table) {
                $table->string("id")->primary();
                $table->string("username")->unique();
                $table->string("password");
            });
        }
    }

    /**
     * Finds a User in database via it's ID. Returns null if not found
     * 
     * @param string $id
     * @return User|null
     */
    public function find(string $id) : ?User
    {
        $resp = DB::table($this->TABLE)->where("id", $id)->first();
        return $this->load($resp);
    }

    /**
     * Saves a User to the database and returns True on success
     *
     * @param User $user
     * @return boolean
     */
    public function save(User $user) : bool
    {
        return DB::table($this->TABLE)->insert(
            $this->dump($user)
        );
    }

    /**
     * Updates a User in the database and returns True on success
     *
     * @param User $user
     * @return boolean
     */
    public function update(User $user) : bool
    {
        return DB::table($this->TABLE)->where("id", $user->getIDString())->update(
            $this->dump($user)
        );
    }


    /**
     * Finds a User in database via it's username. Returns null if not found
     * 
     * @param string $id
     * @return User|null
     */
    public function findByUsername(string $username) : ?User
    {
        $resp = DB::table($this->TABLE)->where("username", $username)->first();
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
        $resp = DB::table($this->TABLE)->where("id", $user->getIDString())->delete();
        return (bool) $resp;
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
        return DB::table($this->TABLE)->where("username", $username)->exists();
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
            "id"=> $user->getIDString(), 
            "username"=> $user->getUsernameString(), 
            "password"=> $user->getPasswordString()
        ];
    }



}















?>