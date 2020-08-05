<?php

namespace App\Resources\Models;

class User 
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;

    /**
     * @param string $username
     * @param string $password
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

    public function getIDString() : string
    {
        return $this->id;
    }

    public function getUsernameString(): string
    {
        return $this->username;
    }

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

    public function updateInformation(array $data){
        foreach (get_object_vars($this) as $k => $v){
            if (isset($data[$k])){
                $this->$k = $data[$k];
            }
        }
    }
}