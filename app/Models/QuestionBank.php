<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionBank extends Model
{
    use HasFactory;

    protected $table = 'question_bank';

    protected $fillable = [
        'user_id',
        'source_paper_code',
        'session',
        'year',
        'subject',
        'topic',
        'difficulty',
        'question_text',
        'question_image',
        'option_type',
        'correct_answer',
        'marks',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    // ─── Relationships ───────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function options()
    {
        return $this->hasMany(QuestionBankOption::class, 'question_id')->orderBy('sort_order');
    }
}
