<?php

namespace App\Contracts\Dao\User;

use Illuminate\Http\Request;
interface UserDaoInterface
{
    public function saveUser(Request $request);

    public function deleteUser(Request $request);

    public function updateUser(Request $request);

    public function changePassword(Request $request);

}

?>
