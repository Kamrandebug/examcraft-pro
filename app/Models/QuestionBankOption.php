<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionBankOption extends Model
{
    use HasFactory;

    public $timestamps = false; // no timestamps in schema

    protected $fillable = [
        'question_id',
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

    public function question()
    {
        return $this->belongsTo(QuestionBank::class, 'question_id');
    }
}
