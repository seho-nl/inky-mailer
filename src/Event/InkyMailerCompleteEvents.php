<?php

namespace SeHo\InkyMailer\Event;

enum InkyMailerCompleteEvents: string
{
    case Success = 'seho_inky_mailer.mailer_event.success';
    case Failed = 'seho_inky_mailer.mailer_event.failed';
}
