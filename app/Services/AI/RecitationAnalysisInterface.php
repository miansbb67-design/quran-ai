<?php

namespace App\Services\AI;

/**
 * Interface for Quran recitation analysis providers.
 * This allows swapping the AI provider without rewriting the application.
 */
interface RecitationAnalysisInterface
{
    /**
     * Analyze a recitation attempt against the target Ayah text.
     *
     * @param string $recognizedText  The text recognized from the user's recitation
     * @param string $targetText      The correct Quranic Arabic text
     * @param array  $options         Additional options (surah, ayah, etc.)
     * @return array{
     *     overall_result: string,
     *     confidence_score: float,
     *     word_analysis: array,
     *     feedback_message: string,
     *     is_estimate: bool
     * }
     */
    public function analyzeRecitation(string $recognizedText, string $targetText, array $options = []): array;
}
