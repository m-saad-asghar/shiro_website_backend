<?php
function generateKeywordsMultilingual($title, $removeStopwords = true) {
    // English stopwords
    $stopwords_en = [
        'a', 'an', 'and', 'are', 'as', 'at', 'be', 'but', 'by', 'for', 'if', 'in',
        'into', 'is', 'it', 'no', 'not', 'of', 'on', 'or', 'such', 'that', 'the',
        'their', 'then', 'there', 'these', 'they', 'this', 'to', 'was', 'will', 'with'
    ];
    // Arabic stopwords
    $stopwords_ar = [
        'في', 'من', 'على', 'و', 'إلى', 'عن', 'أن', 'إن', 'كان', 'ما', 'لا', 'لم', 'لن', 'هل', 'هذا', 'هذه', 'هو', 'هي', 'مع', 'كل'
    ];
    // Merge stopwords
    $stopwords = array_merge($stopwords_en, $stopwords_ar);

    // Convert English text to lowercase (Arabic has no case)
    $title = mb_strtolower($title, 'UTF-8');

    // Remove all characters except Arabic letters, English letters, digits, and spaces
    // Arabic Unicode blocks: \x{0600}-\x{06FF}, \x{0750}-\x{077F}, \x{08A0}-\x{08FF}
    // English letters: a-z
    $title = preg_replace('/[^\p{Arabic}a-z0-9\s]/u', '', $title);

    // Normalize whitespace
    $title = preg_replace('/\s+/u', ' ', $title);

    // Trim spaces
    $title = trim($title);

    // Split into words
    $words = explode(' ', $title);

    // Remove stopwords if requested
    if ($removeStopwords) {
        $words = array_filter($words, function($word) use ($stopwords) {
            return !in_array($word, $stopwords);
        });
    }

    // Remove duplicates
    $words = array_unique($words);

    // Re-index array
    $words = array_values($words);

    // Join with commas
    $keywords = implode(',', $words);

    return $keywords;
}

