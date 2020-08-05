<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use App\Resources\Data\{MessageMapper, UserMapper};
use App\Resources\Models\Message;
use App\Resources\Schema\Schema;
use Symfony\Component\HttpKernel\Exception\{BadRequestHttpException, ConflictHttpException, HttpException, NotFoundHttpException};


class MessageController extends AppController
{
    public function __construct(){
        $this->mapper = new MessageMapper();
    }

    public function getMessages(Request $request, string $user_id)
    {
        $user = (new UserMapper())->find($user_id);
        if (!$user){
            throw new NotFoundHttpException("User ID $user_id cannot be found.");
        }

        $messages = $this->mapper->findBySenderID($user_id);
        return $this->response(200, Schema::parseMessages($messages));
    }

    public function createMessage(Request $request, string $user_id)
    {
        $data = $request->json()->all();
        $data["senderID"] = $user_id;
        if (!isset($data["date"])){{
            $data["date"] = time();
        }}

        if (!$this->_usersExist($data)){
            throw new NotFoundHttpExcetion("Please ensure both the sender and recipient IDs exist");
        }

        $this->validate_data("create", "message", $data);

        $message = $this->_createMessage($data);
        $this->mapper->save($message);

        return $this->response(201, Schema::parseMessage($message));
    }

    public function deleteMessage(Request $request, string $user_id, string $message_id)
    {
        $user = (new UserMapper())->find($user_id);
        
        if (!$user) {
            throw new NotFoundHttpException("User ID $user_id cannot be found");
        }

        $message = $this->mapper->find($message_id);
        
        if (!$message) {
            throw new NotFoundHttpException("Message ID $message_id cannot be found");
        }

        $this->mapper->delete($message); 
        return $this->response(204, null);
    }

    private function _usersExist(array $data) : bool
    {
        $m = new UserMapper();

        $sender = $m->find($data["senderID"]);
        $recipient = $m->find($data["recipientID"]);

        return $sender !== null && $recipient !== null;
    }

    private function _createMessage(array $data) : Message
    {
        return new Message(
            $data["text"], 
            $data["date"],
            $data["senderID"], 
            $data["recipientID"]
        );
    }
}
