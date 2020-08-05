<?php

namespace App\Resources\Models;

/**
 * This model reoresents a simple User object. 
 * 
 * @author Brock Jenken
 */
class User 
{
    /**
     * Unique id to identify the User. Is generated in constructor if one is not provided.
     * 
     * @var string
     */
    protected $id;

    /**
     * Unique Username to identify the User. 
     * 
     * @var string
     */
    protected $username;

    /**
     * Stores the (hashed) password
     * 
     * @var string
     */
    protected $password;

    /**
     * If an ID is not provided to the User a new one is generated.
     *
     * @param string $username
     * @param string $password
     * @param string $id
     */
    public function __construct(string $username,  string $password, string $id = null)
    {
        $this->username = $username;
        $this->password = $password;

        if ($id){
            $this->id = $id;
        }  else {
            $this->id = uniqid(rand());
        }
    }  

    /**
     * @return string
     */
    public function getIDString() : string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUsernameString(): string
    {
        return $this->username;
    }

    /**
     * If instantiated properly, this should return the password hash
     * 
     * @return string
     */
    public function getPasswordString(): string
    {
        return $this->password;
    }

    
    /**
     * @param string $username
     */
    public function setUsername(string $username)
    {
        $this->username = $username;
    }

    /**
     * Streamlines updating of User information.
     * 
     * Will not allow the ID to be updated
     *
     * @param array $data
     * @return void
     */
    public function updateInformation(array $data)
    {
        unset($data["id"]);

        foreach (get_object_vars($this) as $k => $v){
            if (isset($data[$k])){
                $this->$k = $v;
            }
        }
    }
}