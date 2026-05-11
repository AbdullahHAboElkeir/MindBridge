<?php

/**
 * ==========================
 * Observer Design Pattern
 * Used to notify multiple healthcare-related
 * services whenever a patient's mood changes.
 * This helps decouple mood events from dependent logic.
 * ==========================
 */

/**
 * Observer Interface
 * Defines the contract for all mood observers
 */
interface MoodObserverInterface
{
    /**
     * Called when a mood entry is submitted
     * @param array $moodData Contains patient_id, mood_level, notes, triggers, activities
     */
    public function update(array $moodData): void;
}