<?php

namespace SeHo\InkyMailer\Template;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;

class InkyMail implements InkyMailTemplate
{
    private TemplatedEmail $templatedEmail;

    private string $subject;
    private array $contextVariables = [];

    public function __construct()
    {
        $this->templatedEmail = new TemplatedEmail();
    }

    public function getTemplatedEmail(): TemplatedEmail
    {
        return $this->templatedEmail;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @return array<string, mixed>
     */
    public function getContextVariables(): array
    {
        return $this->contextVariables;
    }

    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
    }

    public function setTwigPath(string $htmlTemplate): void
    {
        $this->templatedEmail->htmlTemplate($htmlTemplate);
    }

    public function setInlineCss(string $inlineCss): void
    {
        $this->contextVariables['emailCss'] = $inlineCss;
    }

    public function addContextVariable(string $key, $value): void
    {
        // ToDo: Define and catch reserved variables

        $this->contextVariables[$key] = $value;
    }

    public function to(string|Address $address): void
    {
        $this->templatedEmail->to($address);
    }
}
