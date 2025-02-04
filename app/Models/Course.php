<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class Course extends Model
{
    use HasFactory, AsSource;

    protected $fillable = [
        'name',
        'students',
        'assignments',
        'materials',
        'announcement',
    ];

    protected $casts = [
        'students' => 'array',
        'assignments' => 'array',
        'materials' => 'array',
        'announcement' => 'string',
    ];
}
