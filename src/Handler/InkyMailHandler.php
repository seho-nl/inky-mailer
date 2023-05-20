<?php

namespace SeHo\InkyMailer\Handler;

use SeHo\InkyMailer\Template\InkyMailTemplate;

interface InkyMailHandler
{
    public function sendOne(InkyMailTemplate $email): void;

    public function sendBulk(array $emails): void;
}
