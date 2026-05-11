<?php

/**
 * MoodSubject - Observable
 * Manages mood observers and notifies them of mood changes
 */
class MoodSubject
{
    private array $observers = [];
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Attach an observer to listen for mood changes
     */
    public function attach(MoodObserverInterface $observer): void
    {
        $this->observers[] = $observer;
    }

    /**
     * Notify all observers about a mood update
     */
    public function notify(array $moodData): void
    {
        foreach ($this->observers as $observer) {
            try {
                $observer->update($moodData);
            } catch (Exception $e) {
                // Log error but don't break the mood submission
                error_log("Mood observer error: " . $e->getMessage());
            }
        }
    }

    /**
     * Process a mood submission and notify observers
     */
    public function processMood(array $moodData): void
    {
        // Validate required data
        if (!isset($moodData['patient_id'], $moodData['mood_level'])) {
            throw new InvalidArgumentException("Missing required mood data");
        }

        // Notify all observers
        $this->notify($moodData);
    }
}