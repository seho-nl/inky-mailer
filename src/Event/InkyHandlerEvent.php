<?php

namespace SeHo\InkyMailer\Event;

use SeHo\InkyMailer\Template\InkyMailTemplate;
use Symfony\Contracts\EventDispatcher\Event;

class InkyHandlerEvent extends Event
{
    public function __construct(
        private readonly InkyMailTemplate $inkyMail
    ) {
    }

    public function getInkyMail(): InkyMailTemplate
    {
        return $this->inkyMail;
    }
}
