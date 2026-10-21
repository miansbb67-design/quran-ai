<?php

namespace App\Services;

use DateTime;
use DateTimeInterface;

class IslamicCalendarService
{
    private static array $hijriMonthsEn = [
        1 => 'Muharram',
        2 => 'Safar',
        3 => "Rabi' al-Awwal",
        4 => "Rabi' al-Thani",
        5 => 'Jumada al-Awwal',
        6 => 'Jumada al-Thani',
        7 => 'Rajab',
        8 => "Sha'ban",
        9 => 'Ramadan',
        10 => 'Shawwal',
        11 => "Dhu al-Qi'dah",
        12 => 'Dhu al-Hijjah',
    ];

    private static array $hijriMonthsAr = [
        1 => 'مُحَرَّم',
        2 => 'صَفَر',
        3 => 'رَبِيع الأَوَّل',
        4 => 'رَبِيع الثَّانِي',
        5 => 'جُمَادَى الأُولَى',
        6 => 'جُمَادَى الآخِرَة',
        7 => 'رَجَب',
        8 => 'شَعْبَان',
        9 => 'رَمَضَان',
        10 => 'شَوَّال',
        11 => 'ذُو القَعْدَة',
        12 => 'ذُو الحِجَّة',
    ];

    private static array $daysOfWeekAr = [
        'Sunday' => 'الأَحَد',
        'Monday' => 'الإِثْنَيْن',
        'Tuesday' => 'الثُّلاَثَاء',
        'Wednesday' => 'الأَرْبِعَاء',
        'Thursday' => 'الخَمِيس',
        'Friday' => 'الجُمُعَة',
        'Saturday' => 'السَّبْت',
    ];

    private static array $arabicDigits = [
        '0' => '٠',
        '1' => '١',
        '2' => '٢',
        '3' => '٣',
        '4' => '٤',
        '5' => '٥',
        '6' => '٦',
        '7' => '٧',
        '8' => '٨',
        '9' => '٩',
    ];

    /**
     * Compute current Hijri date based on Kuwaiti/Tabular algorithm with Umm al-Qura alignment
     */
    public static function getHijriDate(?DateTimeInterface $date = null, int $adjustmentDays = 0): array
    {
        $date = $date ? clone $date : new DateTime();
        if ($adjustmentDays !== 0) {
            $date->modify(($adjustmentDays > 0 ? "+{$adjustmentDays}" : "{$adjustmentDays}") . ' days');
        }

        $y = (int) $date->format('Y');
        $m = (int) $date->format('n');
        $d = (int) $date->format('j');

        if ($m < 3) {
            $y -= 1;
            $m += 12;
        }

        $a = floor($y / 100);
        $b = 2 - $a + floor($a / 4);

        $jd = floor(365.25 * ($y + 4716)) + floor(30.6001 * ($m + 1)) + $d + $b - 1524.5;

        // Julian Day to Tabular Islamic Calendar
        $epoch = 1948439.5;
        $l = $jd - $epoch + 10632;
        $n = floor(($l - 1) / 10631);
        $l = $l - 10631 * $n + 354;
        $j = (floor((10985 - $l) / 5316)) * (floor((50 * $l) / 17719)) + (floor($l / 5670)) * (floor((43 * $l) / 15238));
        $l = $l - (floor((30 - $j) / 15)) * (floor((17719 * $j) / 50)) - (floor($j / 16)) * (floor((15238 * $j) / 43)) + 29;

        $month = (int) floor((24 * $l) / 709);
        $day = (int) ($l - floor((709 * $month) / 24));
        $year = (int) (30 * $n + $j - 30);

        $dayNameEn = $date->format('l');
        $dayNameAr = self::$daysOfWeekAr[$dayNameEn] ?? '';

        $monthNameEn = self::$hijriMonthsEn[$month] ?? '';
        $monthNameAr = self::$hijriMonthsAr[$month] ?? '';

        $dayAr = strtr((string) $day, self::$arabicDigits);
        $yearAr = strtr((string) $year, self::$arabicDigits);

        return [
            'day' => $day,
            'day_ar' => $dayAr,
            'month' => $month,
            'year' => $year,
            'year_ar' => $yearAr,
            'day_name_en' => $dayNameEn,
            'day_name_ar' => $dayNameAr,
            'month_name_en' => $monthNameEn,
            'month_name_ar' => $monthNameAr,
            'formatted_en' => "{$day} {$monthNameEn} {$year} AH",
            'formatted_ar' => "{$dayAr} {$monthNameAr} {$yearAr} هـ",
            'gregorian_formatted' => $date->format('l, d F Y'),
            'gregorian_date_short' => $date->format('d M Y'),
            'is_holy_month' => in_array($month, [1, 7, 9, 11, 12]),
        ];
    }
}
