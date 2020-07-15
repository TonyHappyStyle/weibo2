<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UsersController extends Controller
{
    public function create()
    {
        return view('users.create');
    }

    public function show(User $user){
        return view('users.show',['user'=>$user]);
    }

    public function store(Request $request)
    {
        $validate = $this->validate($request,[
            'name'=>'required|max:50|unique:users',
            'email'=>'required|email|max:255|unique:users',
            'password'=>'required|confirm|min:6'
        ]);
        return;
    }
}
