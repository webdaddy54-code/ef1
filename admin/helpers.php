<?php
/**
 * EnterF1.com - Admin shared helpers
 */

/**
 * Create a URL-friendly slug from a string.
 */
function createSlug(string $text): string
{
    $slug = strtolower(trim($text));
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    $slug = trim($slug, '-');
    return $slug ?: 'item';
}
