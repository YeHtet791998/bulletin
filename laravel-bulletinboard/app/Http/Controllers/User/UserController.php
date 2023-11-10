<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Contracts\Services\User\UserServiceInterface;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\UserEditRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class UserController extends Controller
{
    private $userInterface;
    public function __construct(UserServiceInterface $userServiceInterface)
    {
        // $this->middleware('guest');
        $this->userInterface = $userServiceInterface;
    }
    public function index(Request $request)
    {
        $pagesize = 5;
        if($request['page_size']){
            $pagesize = $request['page_size'];
        }
        $userList = DB::table('users as user')
            ->join('users as created_user', 'user.created_user_id', '=', 'created_user.id')
            ->join('users as updated_user', 'user.updated_user_id', '=', 'updated_user.id')
            ->select('user.*', 'created_user.name as created_user', 'updated_user.name as updated_user')
            ->whereNull('user.deleted_at')
            ->orderBy('user.created_at','desc')
            ->Paginate($pagesize);
        return view('user.list', compact('userList'));
    }

    public function deleteUser(Request $request)
    {
        try {
            $this->userInterface->deleteUser($request);
            Toastr::success('User deleted successfully');
            return redirect()->route('userlist');
        } catch (\Exception $e) {
            Toastr::error('An error occurred while deleting the user');
            return redirect()->route('userlist');
        }
    }

    public function showProfile()
    {
        $id = Auth::user()->id;
        $user = User::find($id);
        return view('user.profile', compact('user'));
    }

    public function showProfileEdit()
    {
        if (Storage::disk('public')->exists('images/' . session('uploadProfile'))) {
            Storage::disk('public')->delete('images/' . session('uploadProfile'));
        }
        $id = Auth::user()->id;
        $user = User::find($id);
        return view('user.profile-edit', compact('user'));
    }

    public function submitProfileEdit(UserEditRequest $request)
    {
        $result = $request->validated();
        if ($request->hasFile('profile')) {
            $name = $request->file('profile')->getClientOriginalName();
            $fileName = time() . Auth::user()->id . '.' . $request->file('profile')->getClientOriginalExtension();
            $request->file('profile')->storeAs('public/images/', $fileName);
        } else {
            $fileName = '';
            $name = '';
        }

        session(['ProfileName' => $name]);
        session(['uploadProfile' => $fileName]);
        return redirect()
            ->route('profileeditconfirm')
            ->withInput();
    }

    public function showProfileEditConfirm()
    {
        if (old()) {
            return view('user.profile-edit-confirm');
        }
    }

    public function submitProfileEditConfirm(Request $request)
    {
        if (session('ProfileName')) {
            if (Storage::disk('public')->exists('images/' . $request['old_profile'])) {
                Storage::disk('public')->delete('images/' . $request['old_profile']);
            }
        } else {
            session(['uploadProfile' => Auth::user()->profile]);
        }

        try {
            $this->userInterface->updateUser($request);
            Toastr::success('Profile updated successfully');
            return redirect()
                ->route('userprofile');
        } catch (\Exception $e) {
            Toastr::error('An error occurred while updating profile');
            return redirect()
                ->route('userprofile')->with('error', 'An error occurred while updating profile');
        }
    }

    public function searchUser(Request $request)
    {
        $pagesize = 5;
        if($request['page_size']){
            $pagesize = $request['page_size'];
        }
        $name = $request->input('name');
        $email = $request->input('email');
        $from = $request->input('from');
        $to = $request->input('to');
        $userList = DB::table('users as user')
            ->join('users as created_user', 'user.created_user_id', '=', 'created_user.id')
            ->join('users as updated_user', 'user.updated_user_id', '=', 'updated_user.id')
            ->select('user.*', 'created_user.name as created_user', 'updated_user.name as updated_user')
            ->whereNull('user.deleted_at')
            ->orderBy('user.created_at','desc')
            ->when(!empty($email), function ($query) use ($email) {
                return $query->where('user.email', 'like', "%$email%");
            })
            ->when(!empty($name), function ($query) use ($name) {
                return $query->where('user.name', 'like', "%$name%");
            })
            ->when(!empty($from) && !empty($to), function ($query) use ($from, $to) {
                return $query->whereBetween('user.created_at', [$from, $to]);
            })
            ->paginate($pagesize);
        return view('user.list', compact('userList'));
    }

    public function showChangePassword()
    {
        return view('user.change-password');
    }

    public function submitChangePassword(ChangePasswordRequest $request)
    {
        try {
            $result = $request->validated();
            $this->userInterface->changePassword($request);
            Toastr::success('Password changed successfully');
            return redirect()->route('changepassword');
        } catch (\Exception $e) {
            Toastr::error('An error occurred while changing password');
            return redirect()->route('changepassword');
        }
    }
}
