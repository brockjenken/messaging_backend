<?php

namespace App\Http\Controllers;

use App\Resources\Data\UserMapper;
use App\Resources\Models\User;
use App\Resources\Schema\Parser;
use Illuminate\Http\Response;
use Laravel\Lumen\Http\Request;
use Symfony\Component\HttpKernel\Exception\{ConflictHttpException, NotFoundHttpException};

class UserController extends AppController
{

    public function __construct(){
        $this->mapper = new UserMapper();
    }

    /**
     * Controls the accessing of users.
     *
     * @param Request $request
     * @return Response
     */
    public function getUsers(Request $request) : Response
    {
        $users = $this->mapper->findAll();
        return $this->response(200, Parser::parseUsers($users));
    }

    /**
     * Controls the creation of a user.
     *
     * @param Request $request
     * @return Response
     * @throws ConflictHttpException
     */
    public function createUser(Request $request) : Response
    {
        $data = $request->json()->all();
        $this->validateData("create", "user", $data);

        if ($this->mapper->usernameExists($data["username"])){
            throw new ConflictHttpException("Username {$data["username"]} is already taken");
        }

        $user = $this->_createUser($data);
        $this->mapper->save($user);

        return $this->response(201, Parser::parseUser($user));
    }

    /**
     * Controls the updating of a user. Will prevent a user from changing username to one that already exists.
     *
     * @param Request $request
     * @param string $user_id
     * @return Response
     * @throws NotFoundHttpException
     * @throws ConflictHttpException
     */
    public function updateUser(Request $request, string $user_id) : Response
    {
        $user = $this->mapper->find($user_id);

        if (!$user) {
            throw new NotFoundHttpException("User ID {$user_id} cannot be found");
         }

        $data = $request->json()->all();
        $this->validateData("update", "user", $data);

        if ($user->getUsernameString() !== $data["username"] && $this->mapper->userExists($data["username"])){
            throw new ConflictHttpException( "Username {$data["username"]} is already taken");
        }

        $user->updateInformation($data);
        $this->mapper->save($user);

        return $this->response(200, Parser::parseUser($user));
    }

    /**
     * Controls the deletion of users.
     *
     * @param Request $request
     * @param string $user_id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function deleteUser(Request $request, string $user_id) : Response
    {
        $user = $this->mapper->find($user_id);
        
        if (!$user) {
            throw new NotFoundHttpException("User ID {$user_id} cannot be found");
        }

        $this->mapper->delete($user); 
        return $this->response(204, null);
    }

    /**
     * Helper method used to streamline the creation of a User. 
     * 
     * It creates a User and also hashes the password.
     *
     * @param array $data
     * @return User
     */
    private function _createUser(array $data) : User
    {
        $data["password"] = password_hash($data["password"], PASSWORD_BCRYPT);
        return new User($data["username"], $data["password"]);
    }
}
