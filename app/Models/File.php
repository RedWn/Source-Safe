<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;
    protected $fillable = [
        'projectPath',
        'serverPath',
        'name',
        'projectID',
        'folderID',
    ];
}
