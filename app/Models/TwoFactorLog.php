<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TwoFactorLog extends Model
{
    use HasFactory;
    
    protected $table = 'two_factor_logs';

    protected $fillable = ['code'];
}
