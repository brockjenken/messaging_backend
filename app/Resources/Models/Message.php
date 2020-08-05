<?php

namespace App\Resources\Models;

/**
 * This model represents a simple Message object. 
 * In this model, Messages are created once but not edited. As a result, no setters are present.
 * 
 * @author Brock Jenken
 */
class Message 
{
    
    /**
     * Unique id to identify the Message. Is generated in constructor if one is not provided.
     * 
     * @var string
     */
    protected $id;


    /**
     * Contains the content of the Message.
     * 
     * @var string
     */
    protected $text;

    /**
     * Timestamp representing the Message creation date.
     * 
     * @var int
     */
    protected $date;

    /**
     * Contains the User ID of the sender.
     * 
     * @var string
     */
    protected $senderID;


    /**
     * Contains the User ID of the recipient.
     * 
     * @var string
     */
    protected $recipientID;


     /**
      * If an ID is not provided to the Message a new one is generated.
      *
      * @param string $text
      * @param integer $date
      * @param string $senderID
      * @param string $recipientID
      * @param string $id
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
    public function getTextString() : string
    {
        return $this->text;
    }

    /**
     * @return int
     */
    public function getDateInt() : int
    {
        return $this->date;
    }

    /**
     * @return string
     */
    public function getSenderIDString(): string
    {
        return $this->senderID;
    }

    /**
     * @return string
     */
    public function getRecipientIDString(): string
    {
        return $this->recipientID;
    }

}