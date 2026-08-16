<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPaper extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'type',
        'grade',
        'subject',
        'school_name',
        'exam_date',
        'paper_data',
        'status',
    ];

    protected $casts = [
        'paper_data' => 'array',
        'exam_date'  => 'date',
    ];

    /**
     * The user who owns this paper.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope papers to a specific user.
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Count of questions within this paper (auto: selected MCQs, manual: MCQ blocks).
     */
    public function getQuestionCountAttribute(): int
    {
        if (! is_array($this->paper_data)) {
            return 0;
        }

        if ($this->type === 'auto') {
            return count($this->paper_data['selectedMcqs'] ?? []);
        }

        $count = 0;
        foreach ($this->paper_data['pages'] ?? [] as $page) {
            foreach ($page['blocks'] ?? [] as $block) {
                if (($block['type'] ?? '') === 'mcq') {
                    $count++;
                }
            }
        }

        return $count;
    }
}
