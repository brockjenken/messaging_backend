<?php

namespace App\Resources\Models;

class Message 
{
    
    /**
     * @var string
     */
    protected $id;


    /**
     * @var string
     */
    protected $text;

    /**
     * 
     * @var int
     */
    protected $date;

    /**
     * @var string
     */
    protected $senderID;


    /**
     * @var string
     */
    protected $recipientID;


    /**
     * @param string $text
     * @param int $date
     * @param string senderID
     * @param string recipientID
     * @param string id
     */
    public function __construct(string $text, int $date, string $senderID, string $recipientID, string $id=null)
    {
        $this->text = $text;
        $this->date = $date;
        $this->senderID = $senderID;
        $this->recipientID = $recipientID;

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

    public function getTextString() : string
    {
        return $this->text;
    }

    public function getDateInt() : int
    {
        return $this->date;
    }

    public function getSenderIDString(): string
    {
        return $this->senderID;
    }

    public function getRecipientIDString(): string
    {
        return $this->recipientID;
    }

}