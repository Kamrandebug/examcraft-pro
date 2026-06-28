<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class McqBlock extends Model
{
    use HasFactory;

    protected $fillable = [
        'block_id',
        'stem',
        'stem_image_url',
        'option_type',
        'option_table_headers',
        'marks',
        'layout',
        'correct_answer',
        'show_answer_boxes',
    ];

    protected function casts(): array
    {
        return [
            'option_table_headers' => 'array',
            'show_answer_boxes'    => 'boolean',
        ];
    }

    // ─── Relationships ───────────────────────────────────────────

    public function block()
    {
        return $this->belongsTo(Block::class);
    }

    public function options()
    {
        return $this->hasMany(McqOption::class)->orderBy('sort_order');
    }
}
