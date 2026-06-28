<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    use HasFactory;

    protected $fillable = [
        'page_id',
        'block_type',
        'sort_order',
        'display_number',
        'content',
    ];

    protected function casts(): array
    {
        return [
            'content' => 'array',
        ];
    }

    // ─── Relationships ───────────────────────────────────────────

    public function page()
    {
        return $this->belongsTo(Page::class);
    }

    public function mcqBlock()
    {
        return $this->hasOne(McqBlock::class);
    }
}
