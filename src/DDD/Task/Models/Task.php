<?php
/**
 * Planned Status:
 * [WBS ➔ Spec ➔ Rough Estimation ➔ Backlog ➔ Planned] ➔ [Discovery ➔ 8h WBS ➔ Estimation ➔ In Dev ➔ Unit Test ➔ Alpha* ➔ Integration Test ➔ PR Review] ➔ [In Review] ➔ Beta* ➔ [In QA ➔ Verification Ready] ➔ [In Verification ➔ Release Ready] ➔ [Pre-Release ➔ In Release] ➔ Stable*
 * (*Automated via CI/CD)
 * Discovery ➔ Estimation
 * In Review ➔ Planned
 * In QA ➔ Planned
 * (For quick updates, stay at In Review or In QA)
 * In Verification ➔ In QA
 * <any> ➔ Backlog
 * <any> ➔ Blocked ➔ Planned
 * <any> ➔ Closed ➔ WBS
 *
 * Planned user_id:
 * pm_owner
 * dev_owner
 * lead_owner
 * qa_owner
 * verification_owner
 * ops_owner
 */

namespace DDD\Task\Models;

use App\Models\User;
use Carbon\Carbon;
use Database\Factories\TaskFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    /**
     * @property int $id
     * @property string $title
     * @property string|null $description
     * @property string $status
     * @property int|null $assigned_to
     * @property Carbon|null $due_at
     * @property Carbon $created_at
     * @property Carbon $updated_at
     * @property-read User|null $assignee
     */

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
        'user_id',
        'title',
        'description',
        'status',
        'due_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'due_at' => 'datetime',
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
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class); // foreignKey: 'user_id'
    }

    /**
     * Get the user assigned to the task.
     *
     * @return BelongsTo<User, $this>
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
