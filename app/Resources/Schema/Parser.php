<?php

namespace App\Resources\Schema;

use App\Resources\Models\{User, Message};

/**
 * This class is used to parse object types into JSON objects
 */
class Parser
{
    // Users
    public static function parseUser(User $user, bool $json = TRUE)
    {
        $dump = [
            "id" => $user->getIDString(),
            "username"=> $user->getUsernameString()
            ];

        if ($json) {
            return json_encode($dump);
        } else {
            return $dump;
        }
    }

    public static function parseUsers(array $users)
    {
        $resp = [];
        foreach ($users as $user){
            array_push($resp, Parser::parseUser($user, FALSE));
        }

        return json_encode($resp);
    }

    // Messages
    public static function parseMessage(Message $message, bool $json = TRUE)
    {
        $dump = [
            "id" => $message->getIDString(),
            "text"=> $message->getTextString(),
            "date"=> $message->getDateInt(),
            "senderID"=> $message->getSenderIDString(),
            "recipientID"=> $message->getRecipientIDString(),
            ];

        if ($json) {
            return json_encode($dump);
        } else {
            return $dump;
        }
    }

    public static function parseMessages(array $messages)
    {
        $resp = [];
        foreach ($messages as $message){
            array_push($resp, Parser::parseMessage($message, FALSE));
        }

        return json_encode($resp);
    }

}


