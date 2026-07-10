<?php

namespace Domain\Task\Models;

use App\Models\User;
use Database\Factories\TaskFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory;

    public const STATUS = [
      'todo',
      'in_progress',
      'done',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'status' => 'todo',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
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
    public static function newFactory()
    {
        return TaskFactory::new();
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, foreignKey: 'assigned_to');
    }
}
