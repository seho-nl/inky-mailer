<?php

namespace SeHo\InkyMailer\Template;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;

interface InkyMailTemplate
{
    function getTemplatedEmail(): TemplatedEmail;
    function setInlineCss(string $inlineCss): void;
}
