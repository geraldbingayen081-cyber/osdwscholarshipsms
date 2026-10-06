<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WelfareCaseDocument extends Model
{
    protected $fillable = [
        'welfare_case_id',
        'file_path',
        'file_name',
    ];

    public function welfareCase()
    {
        return $this->belongsTo(WelfareCase::class);
    }
}
