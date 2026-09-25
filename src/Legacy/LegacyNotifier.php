<?php
class LegacyNotifier
{
    public function sendMessage(string $text): void
    {
        echo "[LEGACY] {$text}<br>";
    }
}

class LegacyNotifierAdapter implements Notification
{
    public function __construct(private LegacyNotifier $legacy) {}

    public function send(string $message): void
    {
        $this->legacy->sendMessage($message);
    }
}
