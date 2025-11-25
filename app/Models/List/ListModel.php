<?php

namespace App\Models\List;

use App\Models\Task\Task;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ListModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'lists';

    protected $with = ['tasks'];

    protected $fillable = [
        'name',
    ];

    public function tasks()
    {
        return $this->hasMany(Task::class, 'list_id')->orderBy('position', 'asc');
    }
}
