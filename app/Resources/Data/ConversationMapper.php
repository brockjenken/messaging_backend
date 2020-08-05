<?php

namespace App\Resources\Data;

use App\Resources\Models\{Message, User};

use SQLite3Result;

class ConversationMapper extends BaseMapper {
    /**
     * @val string
     */
    private $TYPE = "conversations";

    public function __construct()
    {
        parent::__construct();
        $this->setUpTable();
    }

    private function setUpTable() : bool
    {
        $fields = ["id TEXT PRIMARY KEY", "text TEXT", "date INTEGER", "senderUsername TEXT", "recipientUsername TEXT"];
        $q = parent::getCreateTableQuery($this->TYPE, $fields);
        $resp = $this->db->exec($q);
        return $resp;
    }

    // public function save(Message $message) : bool
    // {
    //     $fields = ["id", "text", "date", "senderUsername", "recipientUsername"];
    //     $values = [
    //         $message->getIDString(),
    //         $message->getTextString(),
    //         $message->getDateInt(),
    //         $message->getSenderUsernameString(),
    //         $message->getRecipientUsernameString()
    //     ];

    //     $q = parent::getUpsertQuery($this->TYPE, $fields, $values);
    //     $resp = $this->db->insert($q); 
    //     return $resp;
    // }

    public function find(string $id) : ?Message
    {
        $q = parent::getSelectQuery($this->TYPE, ["*"], ["id='{$id}'"]);
        $resp = parent::_find($q);
        return $this->load($resp);
    }

    public function find_by_sender(User $user) : ?array
    {
        $q = parent::getSelectQuery($this->TYPE, ["*"], ["senderUsername='{$user->getUsernameString()}'"]);
        $resp = parent::_find($q);
        return $this->load($resp);
    }

    public function find_by_recipient(User $user) : ?array
    {
        $q = parent::getSelectQuery($this->TYPE, ["*"], ["recipientUsername='{$user->getUsernameString()}'"]);
        $resp = parent::_find($q);
        return $this->load($resp);
    }

    public function find_by_date(int $start, int $end) : ?array
    {
        $q = parent::getSelectQuery($this->TYPE, ["*"], ["date>={$start}", "date<={$end}"]);
        $resp = parent::_find($q);
        return $this->load($resp);
    }

    public function deleteAll() : bool
    {
        $q = parent::getDeleteQuery($this->TYPE);
        $resp = $this->db->exec($q);
        return $resp;
    }

    public function delete(Message $message) : bool
    {
        $q = parent::getDeleteQuery($this->TYPE, ["id='{$message->getIDString()}'"]);
        $resp = $this->db->exec($q);
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
            $dump->senderUsername,
            $dump->recipientUsername,
            $dump->id
            );
    }


}
