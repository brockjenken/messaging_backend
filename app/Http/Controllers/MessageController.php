<?php

namespace App\Http\Controllers;

use App\Resources\Data\{MessageMapper, UserMapper};
use App\Resources\Models\Message;
use App\Resources\Schema\Parser;
use Illuminate\Http\Response;
use Laravel\Lumen\Http\Request;
use Symfony\Component\HttpKernel\Exception\{BadRequestHttpException, ConflictHttpException, HttpException, NotFoundHttpException};


class MessageController extends AppController
{
    public function __construct(){
        $this->mapper = new MessageMapper();
    }

    /**
     * Controls the accessing of Messages for a given user. The user_id represents the recipient, not the sender
     *
     * @param Request $request
     * @param string $user_id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function getMessages(Request $request, string $user_id) : Response
    {
        $user = (new UserMapper())->find($user_id);
        if (!$user){
            throw new NotFoundHttpException("User ID $user_id cannot be found.");
        }

        $messages = $this->mapper->findByRecipientID($user_id);
        return $this->response(200, Parser::parseMessages($messages));
    }

    /**
     * Controls the creating of Messages.
     * 
     * Will automatically override the senderID value to the URL ID and also sets the date to current timestamp.d.
     *
     * @param Request $request
     * @param string $user_id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function createMessage(Request $request, string $user_id) : Response
    {
        $data = $request->json()->all();
        $data["senderID"] = $user_id;
        $data["date"] = time();

        $this->validateData("create", "message", $data);

        if (!$this->_usersExist($data)){
            throw new NotFoundHttpExcetion("Please ensure both the sender and recipient IDs exist");
        }

        $message = $this->_createMessage($data);
        $this->mapper->save($message);

        return $this->response(201, Parser::parseMessage($message));
    }

    /**
     * Controls the deletion of Messages.
     *
     * @param Request $request
     * @param string $user_id
     * @param string $message_id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function deleteMessage(Request $request, string $user_id, string $message_id) : Response
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

    /**
     * Checks if both the sender and recipient exist. Returns True if both exist, otherwise False
     *
     * @param array $data
     * @return boolean
     */
    private function _usersExist(array $data) : bool
    {
        $m = new UserMapper();

        $sender = $m->find($data["senderID"]);
        $recipient = $m->find($data["recipientID"]);

        return $sender !== null && $recipient !== null;
    }

    /**
     * Helper method used to streamline the creation of a Message.
     *
     * @param array $data
     * @return Message
     */
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
