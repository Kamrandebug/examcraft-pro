<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class McqOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'mcq_block_id',
        'label',
        'option_text',
        'option_image_url',
        'option_cells',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'option_cells' => 'array',
        ];
    }

    // ─── Relationships ───────────────────────────────────────────

    public function mcqBlock()
    {
        return $this->belongsTo(McqBlock::class);
    }
}
