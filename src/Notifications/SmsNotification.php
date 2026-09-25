<?php
class SmsNotification
{
    public function __construct(private string $to) {}

    public function send(string $message): void
    {
        echo "[SMS] a {$this->to}: {$message}<br>";
    }
}
