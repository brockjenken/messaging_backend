<?php

namespace App\Resources\Data;

use App\Resources\Models\{Message, User};

class MessageMapper extends BaseMapper {

    /**
     * Holds the name of the database table
     * 
     * @val string
     */
    private $TABLE = "messages";

    public function __construct()
    {
        parent::__construct();
        $this->setUpTable();
    }


    /**
     * Creates table for Messages and returns True on success
     *
     * @return boolean
     */
    private function setUpTable() : bool
    {
        $fields = ["id TEXT PRIMARY KEY", "text TEXT", "date INTEGER", "senderID TEXT", "recipientID TEXT"];
        $q = parent::getCreateTableQuery($this->TABLE, $fields);
        $resp = $this->db->statement($q);
        return $resp;
    }

    /**
     * Saves a Message to the database and returns True on success
     *
     * @param Message $message
     * @return boolean
     */
    public function save(Message $message) : bool
    {
        $fields = ["id", "text", "date", "senderID", "recipientID"];
        $values = $this->dump($message);

        $q = parent::getUpsertQuery($this->TABLE, $fields, $values);
        $resp = $this->db->insert($q); 
        return $resp;
    }

    /**
     * Finds Message in database via it's ID. Returns null if not found
     *
     * @param string $id
     * @return Message|null
     */
    public function find(string $id) : ?Message
    {
        $q = parent::getSelectQuery($this->TABLE, ["*"], ["id='$id'"], ["date ASC"]);
        $resp = parent::_find($q);
        return $this->load($resp);
    }

    /**
     * Finds all Messages sent by a given user. Returns null if nothing is found
     *
     * @param string $id
     * @return array|null
     */
    public function findBySenderID(string $id) : ?array
    {
        $q = parent::getSelectQuery($this->TABLE, ["*"], ["senderID='$id'"], ["date ASC"]);
        $resp = parent::_find($q);

        return $this->loadMany($resp);
    }
    
    /**
     * Finds all Messages received by a given user. Returns null if nothing is found
     *
     * @param string $id
     * @return array|null
     */
    public function findByRecipientID(string $id) : ?array
    {
        $q = parent::getSelectQuery($this->TABLE, ["*"], ["recipientID='$id'"], ["date ASC"]);
        $resp = parent::_find($q);
        return $this->loadMany($resp);
    }

    /**
     * Finds all Messages within a given timerange (start, end). Returns null if nothing is found
     *
     * @param integer $start
     * @param integer $end
     * @return array|null
     */
    public function find_by_date(int $start, int $end) : ?array
    {
        $q = parent::getSelectQuery($this->TABLE, ["*"], ["date>=$start", "date<=$end"], ["date ASC"]);
        $resp = parent::_find($q);
        return $this->loadMany($resp);
    }

    /**
     * Deletes all Messages from the database. Returns True on success
     *
     * @return boolean
     */
    public function deleteAll() : bool
    {
        $q = parent::getDeleteQuery($this->TABLE);
        $resp = $this->db->delete($q);
        return $resp;
    }

    /**
     * Deletes a Message from the database. Returns True on success
     *
     * @param Message $message
     * @return boolean
     */
    public function delete(Message $message) : bool
    {
        $q = parent::getDeleteQuery($this->TABLE, ["id='{$message->getIDString()}'"]);
        $resp = $this->db->delete($q);
        return $resp;
    }

    /**
     * Will load a Message object from the database dump. Returns null if object is empty
     *
     * @param object $dump
     * @return Message|null
     */
    protected function _load(object $dump) : ?Message
    {
        if (!$dump){
            return null;
        }

        return new Message(
            $dump->text,
            $dump->date,
            $dump->senderID,
            $dump->recipientID,
            $dump->id
            );
    }

    /**
     * Converts Message object into an array for saving
     *
     * @param Message $message
     * @return array
     */
    private function dump(Message $message) : array
    {
        return [
            $message->getIDString(),
            $message->getTextString(),
            $message->getDateInt(),
            $message->getSenderIDString(),
            $message->getRecipientIDString()
        ];
    }


}
















?>