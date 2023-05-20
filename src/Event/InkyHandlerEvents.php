<?php

namespace SeHo\InkyMailer\Event;

enum InkyHandlerEvents: string
{
    case Initialize = 'seho_inky_mailer.handler_event.initialize';
    case BeforeSend = 'seho_inky_mailer.handler_event.before_send';
    case AfterSend = 'seho_inky_mailer.handler_event.after_send';
    case Finished = 'seho_inky_mailer.handler_event.finished';
}
