<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class SystemLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'log_type',
        'action',
        'description',
        'subject_type',
        'subject_id',
        'properties',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'properties' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The user who triggered this activity (if authenticated).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Polymorphic relation to the entity affected (Application, WelfareCase, Scholarship, etc.).
     */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Record a new system log entry.
     *
     * @param string $logType e.g., 'Authentication', 'Application', 'Welfare Case', 'Scholarship', 'Compliance', 'Settings'
     * @param string $action e.g., 'login', 'logout', 'create', 'update', 'status_change', 'referral', 'verify'
     * @param string $description Concise human-readable explanation
     * @param Model|null $subject The affected Eloquent model
     * @param array|null $properties Contextual payload, old/new changes, request metadata
     * @param User|null $user The actor (defaults to Auth::user())
     * @return SystemLog
     */
    public static function record(
        string $logType,
        string $action,
        string $description,
        ?Model $subject = null,
        ?array $properties = null,
        ?User $user = null
    ): self {
        $actor = $user ?? Auth::user();

        return static::create([
            'user_id' => $actor?->id,
            'log_type' => $logType,
            'action' => $action,
            'description' => $description,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject?->getKey(),
            'properties' => $properties,
            'ip_address' => Request::ip() ?? '127.0.0.1',
            'user_agent' => Request::userAgent() ?? 'System',
        ]);
    }

    /**
     * Scope to filter logs by type/category.
     */
    public function scopeFilterByType($query, ?string $type)
    {
        if ($type && $type !== 'all') {
            $query->where('log_type', $type);
        }
        return $query;
    }

    /**
     * Scope to filter logs by action.
     */
    public function scopeFilterByAction($query, ?string $action)
    {
        if ($action && $action !== 'all') {
            $query->where('action', $action);
        }
        return $query;
    }

    /**
     * Scope to search logs by keyword in description, IP, action, or user name/email.
     */
    public function scopeSearch($query, ?string $search)
    {
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhere('action', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }
        return $query;
    }

    /**
     * Scope to filter by date timeframe.
     */
    public function scopeFilterByDate($query, ?string $timeframe, ?string $startDate = null, ?string $endDate = null)
    {
        if ($timeframe === 'today') {
            $query->whereDate('created_at', today());
        } elseif ($timeframe === 'yesterday') {
            $query->whereDate('created_at', today()->subDay());
        } elseif ($timeframe === '7days') {
            $query->where('created_at', '>=', now()->subDays(7));
        } elseif ($timeframe === '30days') {
            $query->where('created_at', '>=', now()->subDays(30));
        } elseif ($timeframe === 'custom' && $startDate && $endDate) {
            $query->whereBetween('created_at', ["{$startDate} 00:00:00", "{$endDate} 23:59:59"]);
        }
        return $query;
    }
}
