<?php

namespace App\Models\Task;

use App\Models\Client\Client;
use App\Models\List\ListModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'list_id',
        'client_id',
        'title',
        'start_date',
        'end_date',
        'description',
        'position',
        'priority',
        'status',
    ];

    protected $with = [
        'client',
    ];

    public function listModel()
    {
        return $this->belongsTo(ListModel::class, 'list_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    static public function boot()
    {
        parent::boot();

        static::creating(function ($task) {
            $maxPosition = Task::where('list_id', $task->list_id)->max('position') ?? 0;
            $task->position = $maxPosition + 1;
        });
    }
}
