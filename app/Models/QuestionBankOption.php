<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionBankOption extends Model
{
    use HasFactory;

    protected $table = 'question_bank_options';

    public $timestamps = true;

    protected $fillable = [
        'question_id',
        'label',
        'option_text',
        'option_image_url',
        'option_cells',
        'sort_order',
        'option_a_text',
        'option_a_image',
        'option_b_text',
        'option_b_image',
        'option_c_text',
        'option_c_image',
        'option_d_text',
        'option_d_image',
        'option_e_text',
        'option_e_image',
        'option_f_text',
        'option_f_image',
        'correct_option',
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
