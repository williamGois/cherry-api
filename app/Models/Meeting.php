<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    protected $table = 'meetings';

    protected $fillable = ['meeting_date', 'users', 'title', 'description', 'start_time', 'end_time'];
}
