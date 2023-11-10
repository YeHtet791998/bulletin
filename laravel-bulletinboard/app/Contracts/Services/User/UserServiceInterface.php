<?php

namespace App\Contracts\Services\User;

use Illuminate\Http\Request;
interface UserServiceInterface
{

    public function saveUser(Request $request);

    public function getAllUsers();

    public function deleteUser(Request $request);

    public function getUserById($id);

    public function updateUser(Request $request);

    public function changePassword(Request $request);

}

?>
