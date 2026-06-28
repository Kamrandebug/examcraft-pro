<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectSnapshot extends Model
{
    use HasFactory;

    public $timestamps = false; // only has created_at in schema

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'exam_paper_id',
        'version_label',
        'full_state',
    ];

    protected function casts(): array
    {
        return [
            'full_state' => 'array',
        ];
    }

    // ─── Relationships ───────────────────────────────────────────

    public function examPaper()
    {
        return $this->belongsTo(ExamPaper::class);
    }
}
