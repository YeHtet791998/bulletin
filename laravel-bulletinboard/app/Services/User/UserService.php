<?php

namespace App\Services\User;

use App\Contracts\Dao\User\UserDaoInterface;
use App\Contracts\Services\User\UserServiceInterface;
use Illuminate\Http\Request;
class UserService implements UserServiceInterface
{
    private $userDao;

    public function __construct(UserDaoInterface $userDao)
    {
      $this->userDao = $userDao;
    }

    public function saveUser(Request $request)
    {
       $this->userDao->saveUser($request);
    }

    public function getAllUsers()
    {
      $this->userDao->getAllUsers();
    }

    public function deleteUser(Request $request)
    {
        $this->userDao->deleteUser($request);
    }

    public function getUserById($id)
    {
        $this->userDao->getUserById($id);
    }

    public function updateUser(Request $request)
    {
        $this->userDao->updateUser($request);
    }

    public function changePassword(Request $request)
    {
      $this->userDao->changePassword($request);
    }


}

?>