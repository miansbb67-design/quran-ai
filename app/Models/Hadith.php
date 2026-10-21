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
        // Aliases
        'arabic',
        'english',
        'urdu',
        'book',
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
        ];
    }

    public function getArabicAttribute(): ?string
    {
        return $this->attributes['arabic_text'] ?? null;
    }

    public function setArabicAttribute($value): void
    {
        $this->attributes['arabic_text'] = $value;
    }

    public function getEnglishAttribute(): ?string
    {
        return $this->attributes['english_translation'] ?? null;
    }

    public function setEnglishAttribute($value): void
    {
        $this->attributes['english_translation'] = $value;
    }

    public function getUrduAttribute(): ?string
    {
        return $this->attributes['urdu_translation'] ?? null;
    }

    public function setUrduAttribute($value): void
    {
        $this->attributes['urdu_translation'] = $value;
    }

    public function getBookAttribute(): ?string
    {
        return $this->attributes['source_book'] ?? null;
    }

    public function setBookAttribute($value): void
    {
        $this->attributes['source_book'] = $value;
    }
}
