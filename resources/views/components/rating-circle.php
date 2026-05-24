<?php
/**
 * Rating Circle Component
 * 
 * Expected variables:
 * - $rating : integer (0-100) representing the score.
 */
$rating = $rating ?? 0;
$strokeColor = 'text-green-500';
if ($rating < 70) $strokeColor = 'text-yellow-500';
if ($rating < 40) $strokeColor = 'text-red-500';
if ($rating == 0) $strokeColor = 'text-gray-500';

// Calculate SVG stroke-dasharray (circumference of circle r=16 is ~100)
$circumference = 100.53; // 2 * pi * 16
$dashoffset = $circumference - ($rating / 100) * $circumference;
if ($rating == 0) $dashoffset = $circumference;
?>

<div class="relative w-10 h-10 rounded-full bg-dark/90 backdrop-blur-md flex items-center justify-center border border-gray-700/50 shadow-lg">
    <!-- Background Circle -->
    <svg class="w-full h-full transform -rotate-90 absolute inset-0" viewBox="0 0 36 36">
        <circle cx="18" cy="18" r="16" fill="none" class="stroke-current text-gray-700" stroke-width="2.5"></circle>
        <!-- Foreground Circle -->
        <circle cx="18" cy="18" r="16" fill="none" class="stroke-current <?= $strokeColor ?> transition-all duration-1000 ease-out" 
                stroke-width="2.5" stroke-dasharray="<?= $circumference ?>" stroke-dashoffset="<?= $dashoffset ?>" stroke-linecap="round"></circle>
    </svg>
    <div class="relative text-white text-[10px] font-bold font-sans">
        <?= $rating > 0 ? $rating . '<span class="text-[6px] text-gray-400 absolute top-0.5 -right-1.5">%</span>' : 'NR' ?>
    </div>
</div>
