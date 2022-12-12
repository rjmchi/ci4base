<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\User;

class AuthController extends BaseController
{
    public function __construct(){
        helper('form');
    }
    public function index()
    {
        //
    }

    public function login() {
        //display the login  form

        return view('auth/login');
    }

    public function register() {
        //display the register form

        return view('auth/register');
    }

    public function store() {
        //store new user
        //Validate user fields

         $validated = $this->validate([
            'username'=>[
                'rules'=>'required',
                'errors'=>[
                    'required'=>'Username is required',
                ]
            ],
            'email'=> [
                'rules'=>'required|valid_email|is_unique[users.email]',
                'errors'=>[
                    'required'=>'email address is required',
                    'valid_email'=> 'a valid email address is required',
                    'is_unique'=>'email already on file',
                ]
            ],
            'password'=>[
                'rules'=>'required|min_length[5]',
                'errors'=> [
                    'required'=>'Password is required',
                    'min_length'=>'Password must be at least 5 characters',
                ]
            ],
            'confirm_password'=>[
                'rules'=>'required|matches[password]',
                'errors'=> [
                    'required'=>'Confirmation password is required',
                    'matches'=>'Confirmation password does not match password',
                ]
            ],
        ]);

        if (!$validated){
            return view('auth/register', ['validation'=>$this->validator]);
        }

        $userModel = new User();

        $resp = $userModel->insert([
            'name'=>$this->request->getPost('username'),
            'email'=>$this->request->getPost('email'),
            'password'=>password_hash($this->request->getPost('password'), PASSWORD_BCRYPT),
        ]);

        if (!$resp){
            return redirect()->back()->with('fail', 'Something went wrong');
        }

        $uid = $userModel->getInsertID();
        session()->set('loggedinUser', $uid);
        return redirect()->to('dashboard')->with('success', 'Thank you for registering');
    }

    public function verifyCredentials() {
        // verify login credentials
        $validated = $this->validate([
            'email'=> [
                'rules'=>'required|valid_email|is_not_unique[users.email]',
                'errors'=>[
                    'required'=>'email address is required',
                    'valid_email'=> 'a valid email address is required',
                    'is_not_unique'=>'email is not registered',
                ]
            ],
            'password'=>[
                'rules'=>'required',
                'errors'=> [
                    'required'=>'Password is required',
                ]
            ],
        ]);

        if (!$validated){
            return view('auth/login', ['validation'=>$this->validator]);
        }

        $userModel = new User();
        $user = $userModel->where('email', $this->request->getPost('email'))->first();

        if (!password_verify($this->request->getPost('password'), $user['password'])) {
            session()->setFlashdata('fail', 'Incorrect Password');
            return redirect()->to('login');
        }

        session()->set('loggedinUser', $user['id']);
        return redirect()->to('dashboard');
    }

    public function logout() {
        // log out user
    }
}
