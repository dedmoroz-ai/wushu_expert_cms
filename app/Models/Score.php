<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Score extends Model
{
    protected $fillable = ['registration_id', 'judge_id', 'score'];

    public function judge()
    {
        return $this->belongsTo(User::class, 'judge_id');
    }

    public function registration()
    {
        return $this->belongsTo(Registration::class);
    }
}
