<?php

namespace App\Services\AI;

/**
 * Advanced Quran recitation analysis service.
 * Performs word-by-word alignment, omission/substitution/repetition detection,
 * phonetic similarity analysis, Tajweed and Makharij rule identification,
 * and multi-language spoken teacher guidance generation.
 */
class QuranRecitationAnalysisService implements RecitationAnalysisInterface
{
    /**
     * Analyze a recitation attempt against the target Ayah text.
     */
    public function analyzeRecitation(string $recognizedText, string $targetText, array $options = []): array
    {
        $cleanRecognized = trim($recognizedText);

        // Detect unclear audio or empty speech input
        if (empty($cleanRecognized)) {
            return [
                'overall_result' => 'incorrect',
                'confidence_score' => 0.0,
                'word_analysis' => [],
                'feedback_message' => 'No recitation audio was detected. Please ensure your microphone is enabled and speak clearly near your device.',
                'is_estimate' => true,
                'unclear_audio' => true,
                'correct_words' => 0,
                'total_words' => 0,
                'uncertain_words' => 0,
                'incorrect_words' => 0,
            ];
        }

        // Clean target text from BOM
        $targetText = preg_replace('/^\x{FEFF}/u', '', trim($targetText));

        // Handle optional introductory Bismillah (Tasmiyah) for Surahs 2-114 Ayah 1
        $surah = isset($options['surah']) ? (int) $options['surah'] : null;
        $ayah = isset($options['ayah']) ? (int) $options['ayah'] : null;
        if ($surah > 1 && $surah !== 9 && $ayah === 1) {
            $bismillahPattern = '/^(\x{FEFF})?(بِسْمِ[\s\x{0600}-\x{06FF}]+?ٱلرَّحِيمِ|بسم\s+الله\s+الرحمن\s+الرحيم)\s*/u';
            $strippedRecognized = preg_replace($bismillahPattern, '', $cleanRecognized);
            if (!empty(trim($strippedRecognized))) {
                // Student recited Bismillah before Ayah 1 + the verse: grade on the verse itself
                $recognizedText = trim($strippedRecognized);
            }
        }

        // Normalize both texts for comparison
        $normalizedRecognized = $this->normalizeArabic($recognizedText);
        $normalizedTarget = $this->normalizeArabic($targetText);

        $recognizedArr = array_values(array_filter(explode(' ', $normalizedRecognized)));
        $targetArr = array_values(array_filter(explode(' ', $normalizedTarget)));

        // Keep raw target tokens for displaying authentic Uthmani script
        $rawTargetTokens = array_values(array_filter(explode(' ', trim($targetText))));

        // Perform word-level comparison and Tajweed analysis
        $wordAnalysis = $this->compareWords($recognizedArr, $targetArr, $rawTargetTokens);

        // Calculate overall score
        $totalWords = count($targetArr);
        $correctWords = 0;
        $uncertainWords = 0;
        $incorrectWords = 0;

        foreach ($wordAnalysis as $word) {
            match ($word['status']) {
                'correct' => $correctWords++,
                'uncertain' => $uncertainWords++,
                'incorrect', 'missing', 'extra', 'repeated' => $incorrectWords++,
                default => null,
            };
        }

        $confidenceScore = $totalWords > 0
            ? round(($correctWords / $totalWords) * 100, 2)
            : 0;

        // Determine overall result
        $overallResult = match (true) {
            $confidenceScore >= 85 => 'correct',
            $confidenceScore >= 50 => 'uncertain',
            default => 'incorrect',
        };

        // Generate feedback message
        $feedbackMessage = $this->generateFeedback($overallResult, $confidenceScore, $correctWords, $totalWords);

        return [
            'overall_result' => $overallResult,
            'confidence_score' => $confidenceScore,
            'word_analysis' => $wordAnalysis,
            'feedback_message' => $feedbackMessage,
            'is_estimate' => true,
            'unclear_audio' => false,
            'correct_words' => $correctWords,
            'total_words' => $totalWords,
            'uncertain_words' => $uncertainWords,
            'incorrect_words' => $incorrectWords,
        ];
    }

    /**
     * Compare a single word for targeted single-word practice mode.
     */
    public function compareSingleWord(string $targetWord, string $recognizedWord): array
    {
        $normTarget = $this->normalizeArabic($targetWord);
        $normRec = $this->normalizeArabic($recognizedWord);

        $similarity = $this->wordSimilarity($normRec, $normTarget);
        $isCorrect = $similarity >= 0.80;
        $isUncertain = !$isCorrect && $similarity >= 0.50;

        $status = $isCorrect ? 'correct' : ($isUncertain ? 'uncertain' : 'incorrect');
        $tajweed = $this->detectTajweedAndMakharij($targetWord, $recognizedWord);

        $explanations = $this->generateSpokenExplanations($status, $targetWord, $recognizedWord, $tajweed);

        return [
            'target_word' => $targetWord,
            'recognized_word' => $recognizedWord,
            'status' => $status,
            'matched' => $isCorrect,
            'similarity' => round($similarity, 2),
            'makhraj' => $tajweed['makhraj'],
            'tajweed_rule' => $tajweed['rule'],
            'teacher_tip' => $tajweed['tip'],
            'spoken_explanation_en' => $explanations['en'],
            'spoken_explanation_ur' => $explanations['ur'],
            'spoken_explanation_ar' => $explanations['ar'],
        ];
    }

    /**
     * Compare recognized words against target words using alignment matching.
     */
    protected function compareWords(array $recognized, array $target, array $rawTarget = []): array
    {
        $analysis = [];
        $recognizedIndex = 0;
        $recCount = count($recognized);
        $targetCount = count($target);

        foreach ($target as $i => $targetWord) {
            $displayTarget = $rawTarget[$i] ?? $targetWord;

            if ($recognizedIndex < $recCount) {
                $recWord = $recognized[$recognizedIndex];

                // Check for immediate word repetition (stuttering or saying word twice)
                if ($recognizedIndex > 0 && $recWord === $recognized[$recognizedIndex - 1]) {
                    $tajweed = $this->detectTajweedAndMakharij($displayTarget, $recWord);
                    $explanations = $this->generateSpokenExplanations('repeated', $displayTarget, $recWord, $tajweed);

                    $analysis[] = [
                        'word' => $displayTarget,
                        'target_word' => $displayTarget,
                        'recognized_word' => $recWord,
                        'status' => 'repeated',
                        'matched' => false,
                        'similarity' => 0,
                        'position' => $i + 1,
                        'makhraj' => $tajweed['makhraj'],
                        'tajweed_rule' => $tajweed['rule'],
                        'teacher_tip' => 'This word was recited twice consecutively. Recite once with measured composure.',
                        'spoken_explanation_en' => "The word {$displayTarget} was repeated. Please recite each word once.",
                        'spoken_explanation_ur' => "لفظ {$displayTarget} کو دو بار دہرایا گیا ہے۔ برائے مہربانی اسے ایک بار ادا کریں۔",
                        'spoken_explanation_ar' => "تم تكرار كلمة {$displayTarget}. يُرجى قراءتها مرة واحدة فقط.",
                    ];
                    $recognizedIndex++;
                    continue;
                }

                $similarity = $this->wordSimilarity($recWord, $targetWord);
                $tajweed = $this->detectTajweedAndMakharij($displayTarget, $recWord);

                if ($similarity >= 0.85) {
                    $explanations = $this->generateSpokenExplanations('correct', $displayTarget, $recWord, $tajweed);
                    $analysis[] = [
                        'word' => $displayTarget,
                        'target_word' => $displayTarget,
                        'recognized_word' => $recWord,
                        'status' => 'correct',
                        'matched' => true,
                        'similarity' => round($similarity, 2),
                        'position' => $i + 1,
                        'makhraj' => $tajweed['makhraj'],
                        'tajweed_rule' => $tajweed['rule'],
                        'teacher_tip' => $tajweed['tip'],
                        'spoken_explanation_en' => $explanations['en'],
                        'spoken_explanation_ur' => $explanations['ur'],
                        'spoken_explanation_ar' => $explanations['ar'],
                    ];
                    $recognizedIndex++;
                } elseif ($similarity >= 0.50) {
                    $explanations = $this->generateSpokenExplanations('uncertain', $displayTarget, $recWord, $tajweed);
                    $analysis[] = [
                        'word' => $displayTarget,
                        'target_word' => $displayTarget,
                        'recognized_word' => $recWord,
                        'status' => 'uncertain',
                        'matched' => false,
                        'similarity' => round($similarity, 2),
                        'position' => $i + 1,
                        'makhraj' => $tajweed['makhraj'],
                        'tajweed_rule' => $tajweed['rule'],
                        'teacher_tip' => $tajweed['tip'],
                        'spoken_explanation_en' => $explanations['en'],
                        'spoken_explanation_ur' => $explanations['ur'],
                        'spoken_explanation_ar' => $explanations['ar'],
                    ];
                    $recognizedIndex++;
                } else {
                    // Check if student skipped this word and recited ahead
                    $foundAhead = false;
                    for ($j = $recognizedIndex; $j < min($recognizedIndex + 3, $recCount); $j++) {
                        if ($this->wordSimilarity($recognized[$j], $targetWord) >= 0.70) {
                            // Mark skipped unrecognized tokens as extra
                            for ($k = $recognizedIndex; $k < $j; $k++) {
                                $analysis[] = [
                                    'word' => $recognized[$k],
                                    'target_word' => '',
                                    'recognized_word' => $recognized[$k],
                                    'status' => 'extra',
                                    'matched' => false,
                                    'similarity' => 0,
                                    'position' => $i + 1,
                                    'makhraj' => 'Additional sound',
                                    'tajweed_rule' => 'None',
                                    'teacher_tip' => 'Extra word heard not in verse.',
                                    'spoken_explanation_en' => 'An extra sound was detected.',
                                    'spoken_explanation_ur' => 'ایک اضافی لفظ سنا گیا ہے۔',
                                    'spoken_explanation_ar' => 'تمت قراءة لفظ إضافي.',
                                ];
                            }

                            $isCorrect = $this->wordSimilarity($recognized[$j], $targetWord) >= 0.85;
                            $aheadTajweed = $this->detectTajweedAndMakharij($displayTarget, $recognized[$j]);
                            $explanations = $this->generateSpokenExplanations($isCorrect ? 'correct' : 'uncertain', $displayTarget, $recognized[$j], $aheadTajweed);

                            $analysis[] = [
                                'word' => $displayTarget,
                                'target_word' => $displayTarget,
                                'recognized_word' => $recognized[$j],
                                'status' => $isCorrect ? 'correct' : 'uncertain',
                                'matched' => $isCorrect,
                                'similarity' => round($this->wordSimilarity($recognized[$j], $targetWord), 2),
                                'position' => $i + 1,
                                'makhraj' => $aheadTajweed['makhraj'],
                                'tajweed_rule' => $aheadTajweed['rule'],
                                'teacher_tip' => $aheadTajweed['tip'],
                                'spoken_explanation_en' => $explanations['en'],
                                'spoken_explanation_ur' => $explanations['ur'],
                                'spoken_explanation_ar' => $explanations['ar'],
                            ];
                            $recognizedIndex = $j + 1;
                            $foundAhead = true;
                            break;
                        }
                    }

                    if (!$foundAhead) {
                        $explanations = $this->generateSpokenExplanations('incorrect', $displayTarget, $recWord, $tajweed);
                        $analysis[] = [
                            'word' => $displayTarget,
                            'target_word' => $displayTarget,
                            'recognized_word' => $recWord,
                            'status' => 'incorrect',
                            'matched' => false,
                            'similarity' => round($similarity, 2),
                            'position' => $i + 1,
                            'makhraj' => $tajweed['makhraj'],
                            'tajweed_rule' => $tajweed['rule'],
                            'teacher_tip' => $tajweed['tip'],
                            'spoken_explanation_en' => $explanations['en'],
                            'spoken_explanation_ur' => $explanations['ur'],
                            'spoken_explanation_ar' => $explanations['ar'],
                        ];
                        $recognizedIndex++;
                    }
                }
            } else {
                // Word was omitted / missed
                $tajweed = $this->detectTajweedAndMakharij($displayTarget, '');
                $explanations = $this->generateSpokenExplanations('missing', $displayTarget, '', $tajweed);

                $analysis[] = [
                    'word' => $displayTarget,
                    'target_word' => $displayTarget,
                    'recognized_word' => '',
                    'status' => 'missing',
                    'matched' => false,
                    'similarity' => 0.0,
                    'position' => $i + 1,
                    'makhraj' => $tajweed['makhraj'],
                    'tajweed_rule' => $tajweed['rule'],
                    'teacher_tip' => 'This word was skipped. Please recite every word in sequence without rushing.',
                    'spoken_explanation_en' => $explanations['en'],
                    'spoken_explanation_ur' => $explanations['ur'],
                    'spoken_explanation_ar' => $explanations['ar'],
                ];
            }
        }

        // Mark remaining recognized words as extra
        while ($recognizedIndex < $recCount) {
            $analysis[] = [
                'word' => $recognized[$recognizedIndex],
                'target_word' => '',
                'recognized_word' => $recognized[$recognizedIndex],
                'status' => 'extra',
                'matched' => false,
                'similarity' => 0.0,
                'position' => $targetCount + 1,
                'makhraj' => 'Extra pronunciation',
                'tajweed_rule' => 'None',
                'teacher_tip' => 'Avoid adding extra syllables or words beyond the Quranic verse.',
                'spoken_explanation_en' => 'An extra word was spoken.',
                'spoken_explanation_ur' => 'آیت کے علاوہ اضافی کلام سنا گیا۔',
                'spoken_explanation_ar' => 'تمت قراءة لفظ زائد.',
            ];
            $recognizedIndex++;
        }

        return $analysis;
    }

    /**
     * Detect Makharij, Tajweed rules, and specific phonetic differences for a Quranic word.
     */
    protected function detectTajweedAndMakharij(string $targetWord, ?string $recWord): array
    {
        $cleanTarget = $this->removeDiacritics($targetWord);
        $rules = [];
        $makharij = [];
        $tips = [];

        // 1. Qalqalah Check (ق ط ب ج د)
        if (preg_match('/[قطبجد]/u', $cleanTarget)) {
            // Check if letter has sukoon or is at end of word
            if (preg_match('/[قطبجد][\x{0652}]/u', $targetWord) || preg_match('/[قطبجد]$/u', $cleanTarget)) {
                $rules[] = 'Qalqalah (Echo / Bouncing Sound)';
                $tips[] = 'Ensure a clear bouncing echo (Qalqalah) without adding a vowel sound.';
            }
        }

        // 2. Ghunnah on Mushaddad (نّ, مّ)
        if (preg_match('/[نم]\x{0651}/u', $targetWord) || preg_match('/[نّ|مّ]/u', $targetWord)) {
            $rules[] = 'Ghunnah (2-count Nasalization)';
            $tips[] = 'Hold the nasal sound (Ghunnah) from the nasal cavity (Khayshoom) for 2 counts.';
        }

        // 3. Madd (Long Vowel Elongation)
        if (preg_match('/[\x{0670}\x{0653}]/u', $targetWord) || preg_match('/[اوي]$/u', $cleanTarget)) {
            $rules[] = 'Madd (Vowel Elongation)';
            $tips[] = 'Elongate the long vowel smoothly for the proper count without abrupt clipping.';
        }

        // 4. Makhraj Category
        if (preg_match('/[ءهعحغخ]/u', $cleanTarget)) {
            $makharij[] = 'Throat (Halqi / حلقي)';
        }
        if (preg_match('/[طدتصزسظذثنر]/u', $cleanTarget)) {
            $makharij[] = 'Tongue Tip (Taraf Al-Lisan / طرف اللسان)';
        }
        if (preg_match('/[قك]/u', $cleanTarget)) {
            $makharij[] = 'Deep Tongue / Palate (Aqsa Al-Lisan)';
        }
        if (preg_match('/[ض]/u', $cleanTarget)) {
            $makharij[] = 'Tongue Edge / Upper Molars (Haffat Al-Lisan)';
        }
        if (preg_match('/[فبمو]/u', $cleanTarget)) {
            $makharij[] = 'Lips (Shafatan / شفوي)';
        }

        // 5. Specific Letter Confusion Pairs
        if (!empty($recWord)) {
            $cleanRec = $this->removeDiacritics($recWord);

            if (str_contains($cleanTarget, 'ط') && str_contains($cleanRec, 'ت')) {
                $tips[] = 'Ensure heavy articulation for (ط) with elevation of the tongue, distinct from light (ت).';
                $rules[] = 'Tafkheem (Heavy Emphatic Letter)';
            } elseif (str_contains($cleanTarget, 'ذ') && str_contains($cleanRec, 'ز')) {
                $tips[] = 'The letter (ذ) is pronounced with the tongue tip between the teeth, without whistling like (ز).';
            } elseif (str_contains($cleanTarget, 'ص') && str_contains($cleanRec, 'س')) {
                $tips[] = 'The letter (ص) is heavy and full-mouthed (Isti\'la), distinct from light (س).';
                $rules[] = 'Tafkheem (Isti\'la)';
            } elseif (str_contains($cleanTarget, 'ح') && str_contains($cleanRec, 'ه')) {
                $tips[] = 'Pronounce (ح) from the middle of the throat with friction, distinct from deep chest sound of (ه).';
            } elseif (str_contains($cleanTarget, 'ع') && str_contains($cleanRec, 'ا')) {
                $tips[] = 'Pronounce (ع) firmly from the middle throat, distinct from the smooth glottal stop of Alif (ء).';
            } elseif (str_contains($cleanTarget, 'ض') && (str_contains($cleanRec, 'د') || str_contains($cleanRec, 'ظ'))) {
                $tips[] = 'Pronounce (ض) by pressing the side edge of the tongue against the upper molars with Istitalah.';
            }
        }

        $makhrajStr = !empty($makharij) ? implode(', ', array_unique($makharij)) : 'Oral Articulation (Makhraj)';
        $ruleStr = !empty($rules) ? implode(', ', array_unique($rules)) : 'Standard Recitation & Tartil';
        $tipStr = !empty($tips) ? implode(' ', array_unique($tips)) : 'Listen carefully to the reference Qari and repeat with clear articulation.';

        return [
            'makhraj' => $makhrajStr,
            'rule' => $ruleStr,
            'tip' => $tipStr,
        ];
    }

    /**
     * Generate spoken teacher feedback in English, Urdu, and Arabic.
     */
    protected function generateSpokenExplanations(string $status, string $targetWord, string $recWord, array $tajweed): array
    {
        $cleanWord = $targetWord;

        return match ($status) {
            'correct' => [
                'en' => "Excellent! The word {$cleanWord} was pronounced accurately.",
                'ur' => "ماشاءاللہ! لفظ {$cleanWord} بالکل درست ادا کیا گیا۔",
                'ar' => "أحسنت! تم نطق كلمة {$cleanWord} بشكل صحيح ومتقن.",
            ],
            'uncertain' => [
                'en' => "In the word {$cleanWord}, please pay attention to clear pronunciation. Listen carefully and repeat.",
                'ur' => "لفظ {$cleanWord} میں تلفظ اور مخرج پر توجہ دیں۔ غور سے سنیں اور دہرائیں۔",
                'ar' => "في كلمة {$cleanWord}، يُرجى الانتباه للمخرج وتطبيق التجويد بدقة. استمع وأعد القراءة.",
            ],
            'incorrect' => [
                'en' => "You made a mistake in this word. The correct word is {$cleanWord}. Please listen carefully and repeat it.",
                'ur' => "آپ نے اس لفظ میں غلطی کی ہے۔ صحیح لفظ {$cleanWord} ہے۔ برائے مہربانی غور سے سنیں اور دہرائیں۔",
                'ar' => "لقد حدث خطأ في نطق هذه الكلمة. الكلمة الصحيحة هي {$cleanWord}. استمع جيداً وأعد القراءة.",
            ],
            'missing' => [
                'en' => "The word {$cleanWord} was skipped. Please recite it in sequence with the verse.",
                'ur' => "لفظ {$cleanWord} چھوٹ گیا ہے۔ برائے مہربانی اسے ترتیب سے ادا کریں۔",
                'ar' => "تم تخطي كلمة {$cleanWord}. يُرجى قراءتها في موضعها الصحيح مع الآية.",
            ],
            'repeated' => [
                'en' => "The word {$cleanWord} was repeated. Please recite each word once.",
                'ur' => "لفظ {$cleanWord} کو دہرایا گیا ہے۔ برائے مہربانی اسے ایک ہی بار ادا کریں۔",
                'ar' => "تم تكرار كلمة {$cleanWord}. يُرجى قراءتها مرة واحدة فقط.",
            ],
            default => [
                'en' => "Please listen to the recitation of {$cleanWord} and practice again.",
                'ur' => "برائے مہربانی {$cleanWord} کی تلاوت سنیں اور دوبارہ مشق کریں۔",
                'ar' => "يُرجى الاستماع إلى تلاوة {$cleanWord} والمحاولة مجدداً.",
            ],
        };
    }

    /**
     * Calculate similarity between two Arabic words.
     */
    protected function wordSimilarity(string $word1, string $word2): float
    {
        $clean1 = $this->removeDiacritics($word1);
        $clean2 = $this->removeDiacritics($word2);

        if ($clean1 === $clean2) return 1.0;
        if (empty($clean1) || empty($clean2)) return 0.0;

        $maxLen = max(mb_strlen($clean1), mb_strlen($clean2));
        $distance = $this->mbLevenshtein($clean1, $clean2);

        return max(0, 1 - ($distance / $maxLen));
    }

    /**
     * Multibyte Levenshtein distance.
     */
    protected function mbLevenshtein(string $s1, string $s2): int
    {
        $chars1 = mb_str_split($s1);
        $chars2 = mb_str_split($s2);
        $len1 = count($chars1);
        $len2 = count($chars2);

        $matrix = [];
        for ($i = 0; $i <= $len1; $i++) $matrix[$i][0] = $i;
        for ($j = 0; $j <= $len2; $j++) $matrix[0][$j] = $j;

        for ($i = 1; $i <= $len1; $i++) {
            for ($j = 1; $j <= $len2; $j++) {
                $cost = ($chars1[$i - 1] === $chars2[$j - 1]) ? 0 : 1;
                $matrix[$i][$j] = min(
                    $matrix[$i - 1][$j] + 1,
                    $matrix[$i][$j - 1] + 1,
                    $matrix[$i - 1][$j - 1] + $cost
                );
            }
        }

        return $matrix[$len1][$len2];
    }

    /**
     * Normalize Arabic text for comparison.
     */
    protected function normalizeArabic(string $text): string
    {
        $text = preg_replace('/^\x{FEFF}/u', '', $text);
        $text = str_replace(['أ', 'إ', 'آ', 'ٱ'], 'ا', $text);
        $text = str_replace('ة', 'ه', $text);
        $text = str_replace('ى', 'ي', $text);
        $text = str_replace('ـ', '', $text);
        return preg_replace('/\s+/u', ' ', trim($text));
    }

    /**
     * Remove Arabic diacritics (harakat).
     */
    protected function removeDiacritics(string $text): string
    {
        $text = preg_replace('/^\x{FEFF}/u', '', $text);
        return preg_replace('/[\x{064B}-\x{065F}\x{0670}\x{06D6}-\x{06DC}\x{06DF}-\x{06E4}\x{06E7}\x{06E8}\x{06EA}-\x{06ED}]/u', '', $text);
    }

    /**
     * Generate human-friendly feedback based on the analysis result.
     */
    protected function generateFeedback(string $result, float $score, int $correct, int $total): string
    {
        return match ($result) {
            'correct' => "Excellent! MashaAllah! {$correct} out of {$total} words matched with {$score}% confidence. Keep up the wonderful recitation!",
            'uncertain' => "Good effort! {$correct} out of {$total} words matched ({$score}%). Listen to the reference Qari for the highlighted words and repeat.",
            'incorrect' => "This Ayah needs practice ({$correct}/{$total} words matched). Listen carefully to the voice teacher and reference recitation, then try again.",
            default => 'Please listen to the reference recitation and try again.',
        };
    }
}
