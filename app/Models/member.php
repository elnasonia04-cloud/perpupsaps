<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $table = 'members';

    protected $fillable = [
        'nama',
        'nis_nim',
    ];

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }
}


