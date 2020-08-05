<?php

namespace App\Resources\Data;

use App\Resources\Models\{Message, User};
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
                $table->string("text");
                $table->integer("date");
                $table->string("senderID");
                $table->string("recipientID");
            });
        }
    }
    
    /**
     * Finds Message in database via it's ID. Returns null if not found
     *
     * @param string $id
     * @return Message|null
     */
    public function find(string $id) : ?Message
    {
        $resp = DB::table($this->TABLE)->where("id", $id)->first();
        return $this->load($resp);
    }

    /**
     * Saves a Message to the database and returns True on success
     *
     * @param Message $message
     * @return boolean
     */
    public function save(Message $message) : bool
    {
        return DB::table($this->TABLE)->insert(
            $this->dump($message)
        );
    }

    /**
     * Finds all Messages sent by a given user. Returns null if nothing is found
     *
     * @param string $id
     * @return array|null
     */
    public function findBySenderID(string $id) : ?array
    {
        $resp = DB::table($this->TABLE)->where("senderID", $id)->get();
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
        $resp = DB::table($this->TABLE)->where("recipientID", $id)->get();
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
        $resp = DB::table($this->TABLE)->where(
            ["date", ">=", $start],
            ["date", "<=", $end]
            )->get();
        return $this->loadMany($resp);
    }


    /**
     * Deletes a Message from the database. Returns True on success
     *
     * @param Message $message
     * @return boolean
     */
    public function delete(Message $message) : bool
    {
        $resp = DB::table($this->TABLE)->where("id", $message->getIDString())->delete();
        return (bool) $resp;
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
            "id"=> $message->getIDString(),
            "text"=> $message->getTextString(),
            "date"=> $message->getDateInt(),
            "senderID"=> $message->getSenderIDString(),
            "recipientID"=> $message->getRecipientIDString()
        ];
    }


}
















?>