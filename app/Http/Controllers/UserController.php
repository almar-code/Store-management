<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Handle the incoming request.
     */
      public function AddUser(){
        return view('Users.addUser', []);
    }
    public function Users(){
        return view('Users.Users', []);
    }
    public function AddPermission(){
        return view('Users.AddPermission', []);
    }
    public function Permission(){
        return view('Users.permission', []);
    }
}
