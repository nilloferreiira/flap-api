<?php

namespace App\Models\Task\Elements;

use App\Models\Task\Task;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\SoftDeletes;

class Checklist extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'task_checklist';

    protected $fillable = [
        'task_id',
        'title',
    ];

    protected $with = [
        'items'
    ];

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function items()
    {
        return $this->hasMany(ChecklistItem::class, 'checklist_id');
    }

    /**
     * Cria um checklist item relacionado a este checklist.
     *
     * @param  array  $data  ['description' => string, 'is_completed' => bool]
     * @return ChecklistItem
     */
    public function addItem(array $data): ChecklistItem
    {
        $payload = array_merge([
            'description' => '',
            'is_completed' => false,
        ], $data);

        return $this->items()->create($payload);
    }

    /**
     * Conveniência: adiciona item apenas com descrição.
     */
    public function addItemDescription(string $description): ChecklistItem
    {
        return $this->addItem(['description' => $description]);
    }

    /**
     * Marca/desmarca um item como completo.
     *
     * @param  int   $itemId
     * @param  bool  $completed
     * @return bool
     *
     * @throws ModelNotFoundException se o item não existir
     */
    public function markItemCompleted(int $itemId, bool $completed = true): bool
    {
        $item = $this->items()->find($itemId);

        if (! $item) {
            throw new ModelNotFoundException("Checklist item #{$itemId} não encontrado para este checklist.");
        }

        $item->is_completed = (bool) $completed;
        return $item->save();
    }

    /**
     * Remove (soft delete) um item do checklist.
     */
    public function removeItem(int $itemId): bool
    {
        $item = $this->items()->find($itemId);

        if (! $item) {
            return false;
        }

        return (bool) $item->delete();
    }
}
