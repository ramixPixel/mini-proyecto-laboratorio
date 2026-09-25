<?php
class EmailNotification
{
    public function __construct(private string $to) {}

    public function send(string $message): void
    {
        echo "[EMAIL] para {$this->to}: {$message}<br>";
    }
}
