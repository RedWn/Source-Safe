<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;
    protected $fillable = [
        'serverPath',
        'name',
        'project_id',
        'folder_id',
        'checked_in_by'
    ];

    public function markPendingCheckinsAsDone(): void
    {
        Checkin::where('file_id', $this->id)
            ->where('done', 0)
            ->update(['done' => 1]);
    }
}
