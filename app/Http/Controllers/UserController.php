<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use App\Resources\Data\UserMapper;
use App\Resources\Models\User;
use App\Resources\Schema\Schema;
use Symfony\Component\HttpKernel\Exception\{BadRequestHttpException, ConflictHttpException, HttpException, NotFoundHttpException};


class UserController extends AppController
{

    public function __construct(){
        $this->mapper = new UserMapper();
    }

    /**
     * @param Request request
     */
    public function getUsers(Request $request)
    {
        $users = $this->mapper->findAll();
        return $this->response(200, Schema::parseUsers($users));
    }

    /**
     * @param Request request
     */
    public function createUser(Request $request)
    {
        $data = $request->json()->all();
        $this->validate_data("create", "user", $data);

        if ($this->mapper->userExists($data["username"])){
            throw new ConflictHttpException( "Username {$data["username"]} is already taken");
        }

        $user = $this->_createUser($data);
        $resp = $this->mapper->save($user);

        return $this->response(201, Schema::parseUser($user));
    }

    /**
     * @param Request request
     * @param string user_id
     */
    public function updateUser(Request $request, string $user_id)
    {
        $user = $this->mapper->find($user_id);

        if (!$user) {
            throw new NotFoundHttpException("User ID {$user_id} cannot be found");
         }

        $data = $request->json()->all();
        $this->validate_data("update", "user", $data);

        if ($user->getUsernameString() !== $data["username"] && $this->mapper->userExists($data["username"])){
            throw new ConflictHttpException( "Username {$data["username"]} is already taken");
        }

        $user->updateInformation($data);
        $this->mapper->save($user);

        return $this->response(200, Schema::parseUser($user));
    }
    /**
     * @param Request request
     * @param string user_id
     */
    public function deleteUser(Request $request, string $user_id)
    {
        $user = $this->mapper->find($user_id);
        
        if (!$user) {
            throw new NotFoundHttpException("User ID {$user_id} cannot be found");
        }

        $this->mapper->delete($user); 
        return $this->response(204, null);
    }


    private function _createUser(array $data) : User
    {
        $data["password"] = password_hash($data["password"], PASSWORD_BCRYPT);
        return new User($data["username"], $data["password"]);
    }
}
