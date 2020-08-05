<?php

namespace App\Resources\Data;

use App\Resources\Models\{Message, User};
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MessageMapper extends BaseMapper {

    /**
     * Constant values for MessageMapper field names
     */
    private const ID = "id";
    private const TEXT = "text";
    private const DATE = "date";
    private const SENDER_ID = "senderID";
    private const RECIPIENT_ID = "recipientID";


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
                $table->string(self::ID)->primary();
                $table->string(self::TEXT);
                $table->integer(self::DATE);
                $table->string(self::SENDER_ID);
                $table->string(self::RECIPIENT_ID);
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
        $resp = DB::table($this->TABLE)->where(self::ID, $id)->orderBy(self::DATE, self::ASC)->first();
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
        $resp = DB::table($this->TABLE)->where(self::SENDER_ID, $id)->orderBy(self::DATE, self::ASC)->get();
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
        $resp = DB::table($this->TABLE)->where(self::SENDER_ID, $id)->orderBy(self::DATE, self::ASC)->get();
        return $this->loadMany($resp);
    }

    /**
     * Used to get messages that both users have either sent or received.
     *
     * Messages are returned in sending order, as such is common in many messaging apps
     * 
     * @param string $user1
     * @param string $user2
     * @return array
     */
    public function findConversationByUsers(string $user_id1, string $user_id2) : array
    {
        $resp = DB::table($this->TABLE)
                ->where([[self::SENDER_ID, $user_id1], [self::RECIPIENT_ID, $user_id2]])
                ->orWhere([[self::SENDER_ID, $user_id2], [self::RECIPIENT_ID, $user_id1]])
                ->orderBy(self::DATE, self::DESC)
                ->get();

        return $this->loadMany($resp);
    }

    /**
     * Finds all Messages within a given timerange (start, end). Returns null if nothing is found
     *
     * @param integer $start
     * @param integer $end
     * @return array|null
     */
    public function findByDate(int $start, int $end) : ?array
    {
        $resp = DB::table($this->TABLE)->where(
            [self::DATE, ">=", $start],
            [self::DATE, "<=", $end]
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
        $resp = DB::table($this->TABLE)->where(self::ID, $message->getIDString())->delete();
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
            self::ID=> $message->getIDString(),
            self::TEXT=> $message->getTextString(),
            self::DATE=> $message->getDateInt(),
            self::SENDER_ID=> $message->getSenderIDString(),
            self::RECIPIENT_ID=> $message->getRecipientIDString()
        ];
    }


}
















?>