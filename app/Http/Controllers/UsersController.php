<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Auth;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth',[
            'except'=>['show','create','store','index','confirmEmail'],
        ]);
        $this->middleware('guest',[
            'only'=>['create'],
        ]);
    }

    public function index()
    {
        $users = User::paginate(10);
        return view('users.index',compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function show(User $user){
        $statuses = $user->statuses()
            ->orderBy('created_at','desc')
            ->paginate(10);

        return view('users.show',compact('user','statuses'));
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

        $this->sendEmailConfirmationTo($user);
        session()->flash('warning','已将验证地址发送到您的邮箱，请您查收激活账号！');
        return redirect('/');
//        Auth::login($user);//注册后实现自动登录
//        session()->flash('success','恭喜您注册成功！您将开启一段新的旅程！');
//        return redirect()->route('users.show',[$user]);
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

    public function destroy(User $user)
    {
        $this->authorize('destroy',$user);
        $user->delete();
        session()->flash('success','成功删除用户');
        return back();
    }

    protected function sendEmailConfirmationTo($user)
    {
        $view = 'emails.confirm';
        $data = compact('user');
        $from = 'Summer@example';
        $name = 'Summer';
        $to = $user->email;
        $subject = '谢谢您注册weibo应用，请您验证邮箱！';

        Mail::send($view,$data,function($message)use($from,$name,$to,$subject){
            $message->from($from,$name)->to($to)->subject($subject);
        });
    }

    public function confirmEmail($token)
    {
        $user = User::where('activation_token',$token)->firstOrFail();

        $user->activated = true;
        $user->activation_token = null;
        $user->save();

        Auth::login($user);//注册后实现自动登录
        session()->flash('success','恭喜您注册成功！您将开启一段新的旅程！');
        return redirect()->route('users.show',[$user]);
    }

}
