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
        $this->validate($request,[
            'name'=>'required|max:50|unique:users',
            'email'=>'required|email|max:255|unique:users',
            'password'=>'required|confirmed|min:6'
        ]);

        $user = User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>bcrypt($request->password),
        ]);

        Auth::login($user);//注册后实现自动登录
        session()->flash('success','恭喜您注册成功！您将开启一段新的旅程！');

        return redirect()->route('users.show',[$user]);
    }
}
