<?php

namespace Blog\Http\Controllers\Auth;

use Blog\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Blog\Contracts\SocialServiceInterface;
use Blog\Contracts\UserServiceInterface;
use Socialite;
use Illuminate\Support\Facades\Auth;


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

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }

    public function redirectToProvider()
    {
        return Socialite::driver('facebook')->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return Response
     */
    public function handleProviderCallback(SocialServiceInterface $social_service, UserServiceInterface $user_service)
    {
        $user = Socialite::driver('facebook')->user();
        //dd($user->avatar_original);
        if($social_service->emailExists($user->email)){
            Auth::loginUsingId($user_service->getUserId($user->email));
            return redirect('/home');
        }
        else {
            User::create([
                'name' => $user->name,  
                'email' => $user->email,
                'password' => bcrypt('null'),
            ]);
            $social_service->createSocial($user->name, $user->email, $user->token, $user_service->getUserId($user->email), $user->avatar_original);
        }

        // return redirect()->action('LoginController', [
        //         'email' => $user->email,
        //         'password' => 'null', 
        //     ]);

        // $user->token;
        Auth::loginUsingId($user_service->getUserId($user->email));
        return redirect('/home');
    }

}
