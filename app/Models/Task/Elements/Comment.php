<?php

namespace App\Models\Task\Elements;

use App\Models\Task\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Comment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'task_comments';

    protected $fillable = [
        'task_id',
        'user_id',
        'content',
    ];

    protected $appends = ['author'];

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function createFor(Task $task, User $user, string $content): self
    {
        return DB::transaction(function () use ($task, $user, $content) {
            $comment = self::create([
                'task_id' => $task->id,
                'user_id' => $user->id,
                'content' => $content,
            ]);

            // aqui você pode emitir eventos, notificar, etc.
            return $comment;
        });
    }

    public function getAuthorAttribute()
    {
        return $this->user ? $this->user->name : null;
    }
}
