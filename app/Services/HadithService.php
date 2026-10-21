<?php

namespace App\Services;

use App\Models\Hadith;
use Illuminate\Support\Facades\Cache;

class HadithService
{
    /**
     * Get the Hadith of the day.
     * Deterministically rotates every single day based on day of year.
     */
    public function getDailyHadith(): ?Hadith
    {
        $dayOfYear = (int) date('z'); // 0 to 365
        $count = Hadith::count();

        if ($count === 0) {
            return $this->getFallbackDailyHadith();
        }

        $offset = $dayOfYear % $count;

        return Hadith::orderBy('id')->skip($offset)->first() ?? Hadith::first();
    }

    /**
     * Fallback authentic Hadith if table has not been seeded yet.
     */
    public function getFallbackDailyHadith(): Hadith
    {
        $hadith = new Hadith();
        $hadith->id = 1;
        $hadith->arabic_text = 'خَيْرُكُمْ مَنْ تَعَلَّمَ الْقُرْآنَ وَعَلَّمَهُ';
        $hadith->english_translation = 'The best among you are those who learn the Quran and teach it to others.';
        $hadith->urdu_translation = 'تم میں سے بہترین شخص وہ ہے جو قرآن سیکھے اور اسے دوسروں کو سکھائے۔';
        $hadith->narrator = 'Narrated by Uthman ibn Affan (RA)';
        $hadith->source_book = 'Sahih al-Bukhari 5027';
        $hadith->chapter = 'Virtues of the Quran';
        $hadith->category = 'quran';
        $hadith->grade = 'Sahih';

        return $hadith;
    }

    /**
     * Available Hadith categories
     */
    public static function getCategories(): array
    {
        return [
            'all' => 'All Hadiths',
            'quran' => 'Virtues of the Quran',
            'knowledge' => 'Seeking Knowledge',
            'character' => 'Good Akhlaq & Character',
            'prayer' => 'Prayer & Remembrance',
            'patience' => 'Patience & Gratitude',
            'kindness' => 'Kindness & Mercy',
        ];
    }
}
