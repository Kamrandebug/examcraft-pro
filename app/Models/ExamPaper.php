<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamPaper extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'subject',
        'exam_code',
        'organization',
        'session',
        'year',
        'duration',
        'instructions',
        'materials',
        'typography_preset',
        'typography_state',
        'style_state',
        'cover_footer',
        'page_footer',
        'status',
        'last_saved_at',
    ];

    protected function casts(): array
    {
        return [
            'typography_state' => 'array',
            'style_state'      => 'array',
            'cover_footer'     => 'array',
            'page_footer'      => 'array',
            'last_saved_at'    => 'datetime',
        ];
    }

    // ─── Relationships ───────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pages()
    {
        return $this->hasMany(Page::class);
    }

    public function topics()
    {
        return $this->hasMany(ExamPaperTopic::class);
    }

    public function snapshots()
    {
        return $this->hasMany(ProjectSnapshot::class);
    }
}
