<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_paper_id',
        'page_number',
    ];

    // ─── Relationships ───────────────────────────────────────────

    public function examPaper()
    {
        return $this->belongsTo(ExamPaper::class);
    }

    public function blocks()
    {
        return $this->hasMany(Block::class)->orderBy('sort_order');
    }
}
