<?php

namespace App\Resources\Data;

use App\Resources\Models\{Message, User};

class MessageMapper extends BaseMapper {
    /**
     * @val string
     */
    private $TABLE = "messages";

    public function __construct()
    {
        parent::__construct();
        $this->setUpTable();
    }

    private function setUpTable() : bool
    {
        $fields = ["id TEXT PRIMARY KEY", "text TEXT", "date INTEGER", "senderID TEXT", "recipientID TEXT"];
        $q = parent::getCreateTableQuery($this->TABLE, $fields);
        $resp = $this->db->statement($q);
        return $resp;
    }

    public function save(Message $message) : bool
    {
        $fields = ["id", "text", "date", "senderID", "recipientID"];
        $values = [
            $message->getIDString(),
            $message->getTextString(),
            $message->getDateInt(),
            $message->getSenderIDString(),
            $message->getRecipientIDString()
        ];

        $q = parent::getUpsertQuery($this->TABLE, $fields, $values);
        $resp = $this->db->insert($q); 
        return $resp;
    }

    public function find(string $id) : ?Message
    {
        $q = parent::getSelectQuery($this->TABLE, ["*"], ["id='{$id}'"], ["date ASC"]);
        $resp = parent::_find($q);
        return $this->load($resp);
    }

    public function findBySenderID(string $id) : ?array
    {
        $q = parent::getSelectQuery($this->TABLE, ["*"], ["senderID='$id'"], ["date ASC"]);
        $resp = parent::_find($q);

        return $this->loadMany($resp);
    }

    public function find_by_recipientID(string $id) : ?array
    {
        $q = parent::getSelectQuery($this->TABLE, ["*"], ["recipientID='$id'"], ["date ASC"]);
        $resp = parent::_find($q);
        return $this->loadMany($resp);
    }

    public function find_by_date(int $start, int $end) : ?array
    {
        $q = parent::getSelectQuery($this->TABLE, ["*"], ["date>={$start}", "date<={$end}"], ["date ASC"]);
        $resp = parent::_find($q);
        return $this->loadMany($resp);
    }

    public function deleteAll() : bool
    {
        $q = parent::getDeleteQuery($this->TABLE);
        $resp = $this->db->delete($q);
        return $resp;
    }

    public function delete(Message $message) : bool
    {
        $q = parent::getDeleteQuery($this->TABLE, ["id='{$message->getIDString()}'"]);
        $resp = $this->db->delete($q);
        return $resp;
    }

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


}
















?>