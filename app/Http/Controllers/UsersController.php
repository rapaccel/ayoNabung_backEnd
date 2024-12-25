<?php

namespace App\Http\Controllers;

use App\Models\Otp;
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
    public function register(Request $request)
{
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255',
        'password' => 'required|string|min:6',
        'otp' => 'required|string|max:6',
        'phone_number' => 'required|string',
    ]);

    if ($validator->fails()) {
        return response(['message' => $validator->errors()], 400);
    }
    $otp=Otp::where('phone_number',$request->phone_number)->latest()->first();
    $nohp=$otp->phone_number;
    if (!$otp) {
        return response(['message' => 'OTP not found', "success" => false], 404);
    }
    $request->otp;
    if ($otp->otp == $request->otp && $otp->expires_at > now()) {
        $user = User::create([
            'name' => $request->name,
            'phone_number' => $nohp,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        if (!$user) {
            return response(['message' => 'Failed to create user', "success" => false], 500);
        }
        return response(['data' => $user, 'message' => 'User created successfully', "success" => true], 200);
    } else {
        return response(['message' => 'Invalid or expired OTP', 'success' => false], 400);
    }
}


    public function sendWa(Request $request)
{
    $nohp = $request->nohp;
    $otp = rand(100000, 999999); // Generate OTP
    Otp::create([
        'phone_number' => $nohp,
        'otp' => $otp,
        'expires_at' => now()->addMinutes(5),
    ]);
    // Kirim OTP ke WhatsApp menggunakan Fonnte
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.fonnte.com/send',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => array(
            'target' => $nohp,
            'message' => "Halo! Terima kasih telah mendaftar di aplikasi ayoNabung. Kode OTP Anda adalah: $otp.

    Masukkan kode ini untuk melanjutkan proses verifikasi. Kode ini berlaku selama 5 menit.

    Jika Anda tidak merasa melakukan pendaftaran, harap abaikan pesan ini.

    Salam, Tim ayoNabung"
            ),
            CURLOPT_HTTPHEADER => array(
                'Authorization: nPSwtvHCTFae2x8pghf1'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        
        return response(['message' => 'OTP sent to WhatsApp', 'otp' => $otp], 200); // Untuk testing, jangan tampilkan otp di production
    }


    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            return response()->json(['message' => 'Login Berhasil',"success"=>True,"id"=>$user->id,'user'=>$user], 200);
        }
        return response()->json([
            'message' => 'Login gagal, Email atau Password salah',"success"=>False
        ],401);
    }
    
    Public Function detailUser($id){
        $user=User::find($id);
        return response(['data'=>$user],200);
    }
}
