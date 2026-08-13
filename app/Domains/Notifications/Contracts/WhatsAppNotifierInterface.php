<?php

namespace App\Domains\Notifications\Contracts;

interface WhatsAppNotifierInterface
{
    /** @return string external message id */
    public function send(string $phone, string $message): string;
}
