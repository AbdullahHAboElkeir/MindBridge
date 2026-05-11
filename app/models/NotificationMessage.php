<?php

// ==========================
// Immutable Pattern
// This object is immutable after creation.
// Used to represent a notification payload safely
// so notification data cannot change once built.
// ==========================
class NotificationMessage
{
    private int $userId;
    private string $type;
    private string $title;
    private string $message;
    private string $link;

    public function __construct(int $userId, string $type, string $title, string $message, string $link)
    {
        $this->userId  = $userId;
        $this->type    = $type;
        $this->title   = $title;
        $this->message = $message;
        $this->link    = $link;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'type'    => $this->type,
            'title'   => $this->title,
            'message' => $this->message,
            'link'    => $this->link,
        ];
    }
}
