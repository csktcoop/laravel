<?php

namespace Domain\Task\Models;

use App\Models\User;
use Database\Factories\TaskFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    /** @use HasFactory<TaskFactory> */
    use HasFactory;

    public const STATUS = [
      'todo',
      'in_progress',
      'done',
    ];

    /**
     * {inheritdoc}
     */
    protected $attributes = [
        'status' => 'todo',
    ];

    /**
     * {inheritdoc}
     */
    protected $fillable = [
        'title',
        'description',
        'status',
        'assigned_to',
        'due_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'due_date' => 'date',
    ];

    /**
     * Define or resolved into Database\Factories\Domain\Task\Models\TaskFactory
     */
    public static function newFactory(): TaskFactory
    {
        return TaskFactory::new();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\User, $this>
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, foreignKey: 'assigned_to');
    }
}
