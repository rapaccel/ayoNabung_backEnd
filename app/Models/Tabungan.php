<?php

namespace App\Models;

use App\Models\User;
use App\Models\Kategori;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tabungan extends Model
{
    use HasFactory;
    protected $guarded=['id'];
    public function users(){
        return $this->belongsTo(User::class,'id_user','id');
    }
    public function kategoris(){
        return $this->belongsTo(Kategori::class,'id_kategori','id');
    }
    protected function foto(): Attribute
    {
        return Attribute::make(
            get: fn ($foto) => asset('/storage/' . $foto),
        );
    }
}
