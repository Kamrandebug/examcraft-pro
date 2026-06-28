<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamPaperTopic extends Model
{
    use HasFactory;

    public $timestamps = false; // only has created_at in schema

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'exam_paper_id',
        'topic_name',
        'chapter',
        'question_count',
    ];

    // ─── Relationships ───────────────────────────────────────────

    public function examPaper()
    {
        return $this->belongsTo(ExamPaper::class);
    }
}
