<?php

namespace SeHo\InkyMailer\Event;

use SeHo\InkyMailer\Template\InkyMailTemplate;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Contracts\EventDispatcher\Event;

class InkyMailerCompleteEvent extends Event
{
    public function __construct(
        private readonly InkyMailTemplate $inkyMail,
        private readonly ?TransportExceptionInterface $exception = null
    ) {
    }

    public function getInkyMail(): InkyMailTemplate
    {
        return $this->inkyMail;
    }

    public function getException(): ?TransportExceptionInterface
    {
        return $this->exception;
    }
}
