<?php

namespace App\Http\Controllers\Auth;

use App\Contracts\Services\User\UserServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\SignupRequest;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;
    private $userInterface;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserServiceInterface $userServiceInterface)
    {
        // $this->middleware('guest');
        $this->userInterface = $userServiceInterface;
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    // protected function validator(array $data)
    // {
    //     return Validator::make($data, [
    //         'name' => ['required', 'string', 'max:255'],
    //         'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
    //         'password' => ['required', 'string', 'min:8', 'confirmed'],
    //     ]);
    // }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    // protected function create(array $data)
    // {
    //     return User::create([
    //         'name' => $data['name'],
    //         'email' => $data['email'],
    //         'password' => Hash::make($data['password']),
    //     ]);
    // }

    //show register form
    public function showRegister()
    {
        if (Storage::disk('public')->exists('images/' . session('uploadProfile'))) {
            Storage::disk('public')->delete('images/' . session('uploadProfile'));
        }
        return view('auth.register');
    }

    //submit register form
    public function submitRegister(RegisterRequest $request)
    {
        $result = $request->validated();
        $name = $request->file('profile')->getClientOriginalName();
        $fileName = time() . Auth::user()->id . '.' . $request->file('profile')->getClientOriginalExtension();
        $request->file('profile')->storeAs('public/profiles/', $fileName);
        session(['ProfileName' => $name]);
        session(['uploadProfile' => $fileName]);
        return redirect()
            ->route('registerconfirm')
            ->withInput();
    }

    public function showRegisterConfirm()
    {
        if (old()) {
            return view('auth.register-confirm');
        }
    }

    public function submitRegisterConfirm(Request $request)
    {
        $this->userInterface->saveUser($request);
        return redirect()
            ->route('userlist');
    }

    public function showSignup()
    {
        return view('user.signup');
    }

    public function submitSignup(SignupRequest $request)
    {
        $result = $request->validated();
        $this->userInterface->saveUser($request);
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            if ($request->hasSession()) {
                $request->session()->put('auth.password_confirmed_at', time());
                Toastr::success('Sign up successfully');
                return redirect()
                    ->route('postlist');
            }
        } else {
            Toastr::error('Sign up failed');
            return redirect()
                ->route('postlist')->with('error','Sign up failed');
        }
    }
}
