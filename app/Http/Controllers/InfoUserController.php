<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\View;

class InfoUserController extends Controller
{

    public function create()
    {

        $userAccess = Auth::user()->access;
        return view('users/user-profile', [ 'userAccess' =>  $userAccess]);
    }

    public function updateUser($id)
    {
        $userEdit = User::where('id', $id)->first();
        return view('users/user-profile-store', ['userEdit' => $userEdit]);
    }

    public function updateLoggedUser()
    {

        $attributes = request()->validate([
            'name' => ['required', 'max:50'],
            'email' => ['required', 'email', 'max:50', Rule::unique('users')->ignore(Auth::user()->id)],
            'password' => ['required', 'min:5', 'max:20'],
            'access' => []
        ]);

        
        $attributes['password'] = bcrypt($attributes['password'] );

        if(Auth::user()->access === 'admin'){
            User::where('id',Auth::user()->id)
            ->update([
                'name'    => $attributes['name'],
                'email' => $attributes['email'],
                'password' => $attributes['password'],
                'access' => $attributes['access']
            ]);
        }else {
            User::where('id',Auth::user()->id)
            ->update([
                'name'    => $attributes['name'],
                'email' => $attributes['email'],
                'password' => $attributes['password'],
            ]);
        }



        return redirect('/user-profile')->with('success','Alteração realizada!');
    }

    public function store($id)
    {

        $attributes = request()->validate([
            'name' => ['required', 'max:50'],
            'email' => ['required', 'email', 'max:50', Rule::unique('users')->ignore($id)],
            'password' => ['required', 'min:5', 'max:20'],
            'access' => []
        ]);

        
        $attributes['password'] = bcrypt($attributes['password'] );
    
        User::where('id', $id)
        ->update([
            'name'    => $attributes['name'],
            'email' => $attributes['email'],
            'password' => $attributes['password'],
            'access' => $attributes['access'],
        ]);


        return redirect('/user-management')->with('success','Alteração realizada!');
    }

    public function show()
    {   
        $userAccess = Auth::user()->access;
        $users = User::where('status', 1)->get();
        $arrayUsers = [];
    
        foreach ($users as $user) {
            $arrayUsers[] = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'access' => $user->access,
                'status' => $user->status,
                'created_at' => $user->created_at
            ];
        }
        
        return view('users/user-management', ['users' => $arrayUsers, 'userAccess' =>  $userAccess]);
    }
    
    public function showRemoved()
    {   
        $userAccess = Auth::user()->access;
        $users = User::where('status', 0)->get();
        $arrayUsers = [];
    
        foreach ($users as $user) {
            $arrayUsers[] = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'access' => $user->access,
                'status' => $user->status,
                'created_at' => $user->created_at
            ];
        }
        
        return view('users/user-management-removed', ['users' => $arrayUsers, 'userAccess' =>  $userAccess]);
    }
    


    public function destroy($id){
        User::where('id', $id)
        ->update([
            'status'=> false,
        ]);
    }

    public function recover($id){
        User::where('id', $id)
        ->update([
            'status'=> true,
        ]);
    }
}
