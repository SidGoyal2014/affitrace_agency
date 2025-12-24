<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MmPlatform extends Model
{
    use HasFactory;

    protected $table = 'mmplatforms';

    protected $fillable = [
        'title',
        'description',
        'is_active',
        'created_at',
        'updated_at'
    ];
}
