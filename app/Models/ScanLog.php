<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScanLog extends Model
{
    use HasFactory;
    protected $fillable = [
        'sn',
        'scan_date',
        'pin',
        'verify_mode',
        'work_code',
        'io_mode'
    ];
}
