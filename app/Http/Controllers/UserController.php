<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    //
    public function signupView(){
        return view('user.signup');
    }

    public function signup(Request $request){
        $validateData = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirm',
            'phone' => 'required|digits:10|unique:users,phone',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg',

        ]);
        $imageName = null;
        if($request->hasFile('photo')){
             $imageName = $request->file('photo')->store('image','public');
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => $request->password,
            'photo' => $imageName,

        ]);
    }

    public function login(Request $request){
        $request->validate([
            'login' => 'required',
            'password' => 'required'
        ]);

        $loginType = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        $credential = [
            $loginType => $request->login,
            'password' => $request->password,
        ];

        if(Auth::attempt($credential)){
            return redirect()->route('index');
        }

        return back()->withErrors(['login' => 'Invalid email and phone']);

    }

    public function view(){
        $users = User::all();
        
        return view('user.index',compact('users'));
    }

    public function single($id){
        $users = User::find($id);

        return view('user.single-view',compact('users'));
    }

    public function update($id,Request $request){
        $validateData = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirm',
            'phone' => 'required|digits:10|unique:users,phone',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg',

        ]);
        $user = User::find($id);
        $imageName= public_path('storage').$user->photo;
        if($request->hasFile('photo')){
            $old_file = public_path('storage/').$user->photo;
            if(file_exists($old_file)){
                unlink($old_file);
            }

            $imageName = $request->file('photo')->store('image','public');
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'photo' => $imageName,
        ]);
    }
}
