<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sim extends Model
{
    protected $primaryKey = 'sim_id';
    protected $fillable = [
        'user_id', 'nomor_sim', 'nama', 'tempat_lahir', 'tanggal_lahir',
        'jenis_kelamin', 'alamat', 'pekerjaan', 'jenis_sim', 'nomor_ktp',
        'tanggal_penerbitan', 'masa_berlaku', 'status', 'created_at'
    ];

   
    protected $dates = [
        'tanggal_lahir',
        'tanggal_penerbitan',
        'masa_berlaku',
        'created_at',
        'updated_at',
    ];



    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'sim_id', 'sim_id');
    }
}