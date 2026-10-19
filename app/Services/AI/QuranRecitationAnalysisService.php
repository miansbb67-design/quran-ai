<?php

namespace App\Services\AI;

/**
 * Default recitation analysis provider using text comparison.
 * 
 * NOTE: This is a word-comparison approach. It does NOT detect Tajweed,
 * Makharij, or precise Arabic pronunciation quality. Results are labeled
 * as estimates. For production Quranic assessment, integrate a validated
 * Quran-specific speech analysis provider.
 */
class QuranRecitationAnalysisService implements RecitationAnalysisInterface
{
    /**
     * Analyze a recitation attempt against the target Ayah text.
     */
    public function analyzeRecitation(string $recognizedText, string $targetText, array $options = []): array
    {
        // Normalize both texts for comparison
        $recognizedWords = $this->normalizeArabic($recognizedText);
        $targetWords = $this->normalizeArabic($targetText);

        $recognizedArr = array_filter(explode(' ', $recognizedWords));
        $targetArr = array_filter(explode(' ', $targetWords));

        // Perform word-level comparison
        $wordAnalysis = $this->compareWords($recognizedArr, $targetArr);

        // Calculate overall score
        $totalWords = count($targetArr);
        $correctWords = 0;
        $uncertainWords = 0;
        $incorrectWords = 0;

        foreach ($wordAnalysis as $word) {
            match ($word['status']) {
                'correct' => $correctWords++,
                'uncertain' => $uncertainWords++,
                'incorrect', 'missing', 'extra' => $incorrectWords++,
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
            'correct_words' => $correctWords,
            'total_words' => $totalWords,
            'uncertain_words' => $uncertainWords,
            'incorrect_words' => $incorrectWords,
        ];
    }

    /**
     * Compare recognized words against target words using similarity matching.
     */
    protected function compareWords(array $recognized, array $target): array
    {
        $analysis = [];
        $recognizedIndex = 0;

        foreach ($target as $i => $targetWord) {
            if (isset($recognized[$recognizedIndex])) {
                $recWord = $recognized[$recognizedIndex];
                $similarity = $this->wordSimilarity($recWord, $targetWord);

                if ($similarity >= 0.85) {
                    $analysis[] = [
                        'target_word' => $targetWord,
                        'recognized_word' => $recWord,
                        'status' => 'correct',
                        'similarity' => $similarity,
                        'position' => $i + 1,
                    ];
                    $recognizedIndex++;
                } elseif ($similarity >= 0.5) {
                    $analysis[] = [
                        'target_word' => $targetWord,
                        'recognized_word' => $recWord,
                        'status' => 'uncertain',
                        'similarity' => $similarity,
                        'position' => $i + 1,
                    ];
                    $recognizedIndex++;
                } else {
                    // Check if the word was skipped (missing)
                    $foundAhead = false;
                    for ($j = $recognizedIndex; $j < min($recognizedIndex + 3, count($recognized)); $j++) {
                        if ($this->wordSimilarity($recognized[$j], $targetWord) >= 0.7) {
                            // Mark skipped words as extra
                            for ($k = $recognizedIndex; $k < $j; $k++) {
                                $analysis[] = [
                                    'target_word' => '',
                                    'recognized_word' => $recognized[$k],
                                    'status' => 'extra',
                                    'similarity' => 0,
                                    'position' => $i + 1,
                                ];
                            }
                            $analysis[] = [
                                'target_word' => $targetWord,
                                'recognized_word' => $recognized[$j],
                                'status' => $this->wordSimilarity($recognized[$j], $targetWord) >= 0.85 ? 'correct' : 'uncertain',
                                'similarity' => $this->wordSimilarity($recognized[$j], $targetWord),
                                'position' => $i + 1,
                            ];
                            $recognizedIndex = $j + 1;
                            $foundAhead = true;
                            break;
                        }
                    }

                    if (!$foundAhead) {
                        $analysis[] = [
                            'target_word' => $targetWord,
                            'recognized_word' => $recWord,
                            'status' => 'incorrect',
                            'similarity' => $similarity,
                            'position' => $i + 1,
                        ];
                        $recognizedIndex++;
                    }
                }
            } else {
                // Word was not recited (missing)
                $analysis[] = [
                    'target_word' => $targetWord,
                    'recognized_word' => '',
                    'status' => 'missing',
                    'similarity' => 0,
                    'position' => $i + 1,
                ];
            }
        }

        // Mark remaining recognized words as extra
        while ($recognizedIndex < count($recognized)) {
            $analysis[] = [
                'target_word' => '',
                'recognized_word' => $recognized[$recognizedIndex],
                'status' => 'extra',
                'similarity' => 0,
                'position' => count($target) + 1,
            ];
            $recognizedIndex++;
        }

        return $analysis;
    }

    /**
     * Calculate similarity between two Arabic words.
     */
    protected function wordSimilarity(string $word1, string $word2): float
    {
        // Remove diacritics for base comparison
        $clean1 = $this->removeDiacritics($word1);
        $clean2 = $this->removeDiacritics($word2);

        if ($clean1 === $clean2) return 1.0;
        if (empty($clean1) || empty($clean2)) return 0.0;

        // Use Levenshtein-like comparison on character level
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
        // Normalize various Arabic letter forms
        $text = str_replace(['أ', 'إ', 'آ', 'ٱ'], 'ا', $text);
        $text = str_replace('ة', 'ه', $text);
        $text = str_replace('ى', 'ي', $text);

        // Remove tatweel
        $text = str_replace('ـ', '', $text);

        // Trim and normalize whitespace
        $text = preg_replace('/\s+/u', ' ', trim($text));

        return $text;
    }

    /**
     * Remove Arabic diacritics (harakat).
     */
    protected function removeDiacritics(string $text): string
    {
        // Unicode ranges for Arabic diacritics
        return preg_replace('/[\x{064B}-\x{065F}\x{0670}\x{06D6}-\x{06DC}\x{06DF}-\x{06E4}\x{06E7}\x{06E8}\x{06EA}-\x{06ED}]/u', '', $text);
    }

    /**
     * Generate human-friendly feedback based on the analysis result.
     */
    protected function generateFeedback(string $result, float $score, int $correct, int $total): string
    {
        return match ($result) {
            'correct' => $this->getCorrectFeedback($score, $correct, $total),
            'uncertain' => $this->getUncertainFeedback($score, $correct, $total),
            'incorrect' => $this->getIncorrectFeedback($score, $correct, $total),
            default => 'Please try again.',
        };
    }

    protected function getCorrectFeedback(float $score, int $correct, int $total): string
    {
        $messages = [
            "Excellent! MashaAllah! {$correct} out of {$total} words matched with {$score}% confidence. Keep up the wonderful work!",
            "Very good recitation! The system recognized {$correct}/{$total} words correctly. May Allah bless your efforts!",
            "SubhanAllah! Great job! Your recitation matched {$score}% of the target. Continue practicing!",
        ];
        return $messages[array_rand($messages)];
    }

    protected function getUncertainFeedback(float $score, int $correct, int $total): string
    {
        $messages = [
            "Good effort! {$correct} out of {$total} words were recognized. Let's practice the uncertain words again. Listen to the reference recitation and try once more.",
            "You're making progress! Some words need more practice. Listen carefully to the correct recitation and try to match the pronunciation.",
            "Keep going! {$correct}/{$total} words matched. The highlighted words need attention. Listen and repeat to improve.",
        ];
        return $messages[array_rand($messages)];
    }

    protected function getIncorrectFeedback(float $score, int $correct, int $total): string
    {
        $messages = [
            "Let's practice this Ayah again. The recognition system was uncertain about several words. Listen to the reference recitation carefully and try once more. Don't worry — practice makes perfect!",
            "This Ayah needs more practice. Listen to the correct recitation first, then try reciting slowly word by word. You recognized {$correct}/{$total} words.",
            "Take your time with this Ayah. Start by listening to the full recitation, then practice each word. Remember, learning the Quran is a journey!",
        ];
        return $messages[array_rand($messages)];
    }
}
