<?php

namespace App\Resources\Models;

class Conversation 
{
    /**
     * @var string 
     */
    protected $id;

    /**
     * @var User[]
     */
    protected $users;

    /**
     * @var Message[]
     */
    protected $messages;

    /**
     * @param User[] $users
     * @param Message[] $messages
     * @param string $id
     */

    public function __construct(array $users,  array $messages, $id=null)
    {
        $this->users = $users;
        $this->messages = $messages;

        if ($id){
            $this->id = $id;
        }  else {
            $this->id = uniqid(rand(), true);
        }
    }

    public function getIDString() : string
    {
        return $this->id;
    }

    public function getMessages(): array
    {
        return $this->meessages;
    }

    public function getUsers(): array
    {
        return $this->users;
    }

    
    /**
     * @param string $username
     */
    public function setUsername(string $username)
    {
        $this->username = $username;
    }
}
