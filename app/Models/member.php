<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $table = 'members';

    protected $fillable = [
        'nama',
        'nis_nim',
        'no_hp',
        'alamat',
        'foto',
    ];

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }
}
