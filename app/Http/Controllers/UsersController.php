<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{
    public function index(){
        $users=User::all();
        return response(['data'=>$users],200);
    }
    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'foto' => 'image',
            'password' => 'required|string|min:6',
        ]);
    
        if($validator->fails()){
            return response(['message' => $validator->errors()], 400);
        }
    
        $fotoPath = null; 
    
        if ($request->hasFile('foto')) {
            $foto = $request->file('foto');
            $fotoPath = $foto->store('fotos', 'public'); 
    
            if (!$fotoPath) {
                return response(['message' => 'Failed to upload photo'], 500);
            }
        }
    
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'foto' => $fotoPath, 
            'password' => Hash::make($request->password),
        ]);
        if (!$user) {
            return response(['message' => 'Failed to create user',"success"=>False], 500); 
        }
        return response(['data' => $user, 'message' => 'User created successfully',"success"=>True], 200);
    }
    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            return response()->json(['message' => 'Login Berhasil',"success"=>True,"id"=>$user->id], 200);
        }
        return response()->json([
            'message' => 'Login gagal, Email atau Password salah',"success"=>False
        ]);
    }
    
    Public Function detailUser($id){
        $user=User::find($id);
        return response(['data'=>$user],200);
    }
}
