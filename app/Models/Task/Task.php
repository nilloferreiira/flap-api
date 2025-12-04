<?php

namespace App\Models\Task;

use App\Models\Client\Client;
use App\Models\List\ListModel;
use App\Models\Task\Elements\Link;
use App\Models\Task\Elements\Comment;
use App\Models\Task\Elements\Checklist;
use App\Models\Task\Elements\TaskMember;
use App\Models\User;
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
        'members',
        'links',
        'comments',
        'checklists',
    ];


    static public function boot()
    {
        parent::boot();

        static::creating(function ($task) {
            $maxPosition = Task::where('list_id', $task->list_id)->max('position') ?? 0;
            $task->position = $maxPosition + 1;
        });
    }

    public function listModel()
    {
        return $this->belongsTo(ListModel::class, 'list_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    /**
     * Links relacionados à task
     */
    public function links()
    {
        return $this->hasMany(Link::class, 'task_id');
    }

    /**
     * Comentários da task
     */
    public function comments()
    {
        return $this->hasMany(Comment::class, 'task_id');
    }

    /**
     * Checklists pertencentes à task
     */
    public function checklists()
    {
        return $this->hasMany(Checklist::class, 'task_id');
    }

    /**
     * Relação para os registros de membros (task_members)
     */
    public function taskMembers()
    {
        return $this->hasMany(TaskMember::class, 'task_id');
    }

    /**
     * Usuários membros da task (através da tabela task_members)
     */
    public function members()
    {
        return $this->belongsToMany(User::class, 'task_members', 'task_id', 'user_id')->where('task_members.deleted_at', null);
    }
}
