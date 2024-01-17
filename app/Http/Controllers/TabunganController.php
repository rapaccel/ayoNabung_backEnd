<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tabungan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class TabunganController extends Controller
{
    public function __construct()
{
    $this->middleware('auth.tabungan');
}
    public function index($id){
        $user = User::find($id);
        if (!$user) {
            return response(['message' => 'User not found'], 404);
        }

        $tabungan = $user->tabungans;
        return response(['message'=>"success",'data'=>$tabungan,"success"=>True],200);
    }
    public function store(Request $request){
        $validator=Validator::make($request->all(),[
            'jumlah'=>'required',
            'jenis'=>'required|in:kebutuhan,keinginan,investasi',
            'tipe'=>'required|string|in:pemasukan,pengeluaran',
            'keterangan'=>'required|string',
            'id_user'=>'required'
        ]);
        if($validator->fails()){
            return response(['message'=>$validator->errors()],400);
        }
        $user = User::find($request->id_user);
        if (!$user) {
            return response(['message' => 'User not found',"success"=>False], 404);
        }
        $tabungan = new Tabungan([
            'jumlah' => $request->jumlah,
            'jenis' => $request->jenis,
            'tipe' => $request->tipe,
            'keterangan' => $request->keterangan,
        ]);
        $user->tabungans()->save($tabungan);
        return response(['message'=>"success",'data'=>$tabungan,"success"=>True],200);
    }
    public function update(Request $request, $id) {
        $validator = Validator::make($request->all(), [
            'jumlah' => 'required',
            'jenis' => 'required|in:kebutuhan,keinginan,investasi',
            'tipe' => 'required|string|in:pemasukan,pengeluaran',
            'keterangan' => 'required|string',
            'id_user' => 'required'
        ]);
        if ($validator->fails()) {
            return response(['message' => $validator->errors()], 400);
        }
        
        $user = User::find($request->id_user);
        if (!$user) {
            return response(['message' => 'User not found'], 404);
        }
        
        $tabungan = Tabungan::find($id);
        if (!$tabungan) {
            return response(['message' => 'Tabungan not found',"success"=>False], 404);
        }
        
        $tabungan->jumlah = $request->input('jumlah');
        $tabungan->jenis = $request->input('jenis');
        $tabungan->tipe = $request->input('tipe');
        $tabungan->keterangan = $request->input('keterangan');
        $tabungan->save();
        
        return response(['message' => "success", 'data' => $tabungan,"success"=>True], 200);
    }
    public function delete($id) {
        $tabungan = Tabungan::find($id);
        if (!$tabungan) {
            return response(['message' => 'Tabungan not found',"success"=>False], 404);
        }
        
        $tabungan->delete();
        
        return response(['message' => "Berhasil menghapus data","success"=>True], 200);
    }
    public function getDetail($id)
    {
        try {
            $tabungan = Tabungan::findOrFail($id);
            return response()->json(['success' => true, 'data' => $tabungan], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Tabungan not found'], 404);
        }
    }
}
