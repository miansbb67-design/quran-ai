<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hadith extends Model
{
    use HasFactory;

    protected $table = 'hadiths';

    protected $fillable = [
        'arabic_text',
        'english_translation',
        'urdu_translation',
        'narrator',
        'source_book',
        'chapter',
        'category',
        'grade',
        'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
        ];
    }
}
