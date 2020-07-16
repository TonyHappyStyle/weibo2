<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth',[
            'except'=>['show','create','store'],
        ]);
        $this->middleware('guest',[
            'only'=>['create'],
        ]);
    }

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

    public function edit(User $user)
    {
        $this->authorize('update',$user);
        return view('users.edit',compact('user'));
    }

    public function update(User $user,Request $request)
    {
        $this->authorize('update',$user);
        $this->validate($request,[
            'name'=>'required|max:50',
            'password'=>'nullable|confirmed|min:6',
        ]);

        $user->update([
            'name'=>$request->name,
            'password'=>bcrypt($request->password),
        ]);
        session()->flash('success','个人信息修改成功');
        return redirect()->route('users.show',compact('user'));
    }
}
