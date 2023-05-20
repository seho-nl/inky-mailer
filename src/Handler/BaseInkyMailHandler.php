<?php

namespace SeHo\InkyMailer\Handler;

use SeHo\InkyMailer\Event\InkyHandlerEvent;
use SeHo\InkyMailer\Event\InkyHandlerEvents;
use SeHo\InkyMailer\Event\InkyMailerCompleteEvent;
use SeHo\InkyMailer\Event\InkyMailerCompleteEvents;
use SeHo\InkyMailer\Exception\InkyMailerException;
use SeHo\InkyMailer\Template\InkyMailTemplate;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

abstract class BaseInkyMailHandler implements InkyMailHandler
{
    private EventDispatcherInterface $dispatcher;
    private ParameterBagInterface $params;
    private MailerInterface $mailer;

    /* @internal Injected by the Compiler Pass */
    final public function setDispatcher(EventDispatcherInterface $dispatcher): void
    {
        $this->dispatcher = $dispatcher;
    }

    /* @internal Injected by the Compiler Pass */
    final public function setParameterBag(ParameterBagInterface $params): void
    {
        $this->params = $params;
    }

    /* @internal Injected by the Compiler Pass */
    final public function setMailer(MailerInterface $mailer): void
    {
        $this->mailer = $mailer;
    }

    public function getParameterBag(): ParameterBagInterface
    {
        return $this->params;
    }

    abstract public function sendOne(InkyMailTemplate $email): void;

    abstract public function sendBulk(array $emails): void;

    final public function doSend(InkyMailTemplate $email): void
    {
        // ToDo: Validate email so we can throw the correct Exception if somethings wrong...

        $inkyHandlerEvent = new InkyHandlerEvent($email);

        $this->dispatcher->dispatch($inkyHandlerEvent, InkyHandlerEvents::Initialize->value);

        $this->beforeSendMail($email);
        $this->dispatcher->dispatch($inkyHandlerEvent, InkyHandlerEvents::BeforeSend->value);

        $this->mailerSend($email);

        $this->afterSendMail($email->getTemplatedEmail());
        $this->dispatcher->dispatch($inkyHandlerEvent, InkyHandlerEvents::AfterSend->value);

        $this->dispatcher->dispatch($inkyHandlerEvent, InkyHandlerEvents::Finished->value);
    }

    private function beforeSendMail(InkyMailTemplate $email): void
    {
        $templatedEmail = $email->getTemplatedEmail();

        if ($this->params->has('seho_inky_mailer.address.email')) {
            $templatedEmail->from(new Address(
                $this->params->get('seho_inky_mailer.address.email'),
                $this->params->get('seho_inky_mailer.address.name') ?? ''
            ));
        }

        $templatedEmail->subject($this->buildSubject($email->getSubject()));

        if (null === $templatedEmail->getHtmlTemplate()) {
            throw new InkyMailerException('No template set in InkyMailTemplate.');
        }

        $templatedEmail->context($email->getContextVariables());
    }

    private function afterSendMail(TemplatedEmail $email): void
    {
        // REFACTOR TO EVENT
    }

    private function mailerSend(InkyMailTemplate $email): void
    {
        try {
            $this->mailer->send($email->getTemplatedEmail());
        } catch (TransportExceptionInterface $e) {
            $inkyHandlerEvent = new InkyMailerCompleteEvent($email, $e);
            $this->dispatcher->dispatch($inkyHandlerEvent, InkyMailerCompleteEvents::Failed->value);

            return;
        }

        $inkyHandlerEvent = new InkyMailerCompleteEvent($email);
        $this->dispatcher->dispatch($inkyHandlerEvent, InkyMailerCompleteEvents::Success->value);
    }

    private function buildSubject(string $subject): string
    {
        return implode('', [
            $this->params->get('seho_inky_mailer.subject.prefix'),
            $subject,
            $this->params->get('seho_inky_mailer.subject.suffix'),
        ]);
    }
}
