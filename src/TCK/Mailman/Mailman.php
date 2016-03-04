<?php
namespace TCK\Mailman;

use Config, Mail;
use Illuminate\Contracts\Mail\Mailer;

abstract class Mailman
{
    /**
     * @var Mailer
     */
    protected $mailer;

    /**
     * @param Mailer $mailer
     */
    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Sends Email
     *
     * @param    int    $user
     * @param    string $view
     * @param    string $subject
     * @param    array  $data
     * @return    bool
     */
    public function sendTo($user, $subject, $view, $data = [])
    {
        $recipient = is_object($user) ? $user->email : $user;

        $data['user'] = $user;

        try {
            // Queue it up and send it!
            $this->mailer->send($view, $data, function ($m) use ($recipient, $subject, $data) {
                $this->overwriteFrom($m, $data);
                $this->addAttachment($m, $data);
                $this->setCC($m, $data);
                $this->setBC($m, $data);

                return $m->to($recipient)->subject($subject);
            });
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @param $m
     * @param $data
     */
    function overwriteFrom($m, $data)
    {
        if (isset( $data['overwriteFrom'] )) {
            $m->from($data['email'], $this->getFromName($data));
        }
    }

    /**
     * @param      $data
     * @param null $fname
     * @param null $surname
     * @return string
     */
    public function getFromName($data, $fname = null, $surname = null)
    {
        if (isset( $data['first_name'] )) {
            $fname = $data['first_name'] . ' ';
        }

        if (isset( $data['surname'] )) {
            $surname = $data['surname'];
        }

        return $fname . $surname;
    }

    /**
     * @param $m
     * @param $data
     */
    function addAttachment($m, $data)
    {
        if (isset( $data['attachment'] )) {
            return $m->attach($data['attachment'], ['mime' => 'application/pdf']);
        }
    }

    /**
     * @param $m
     * @param $data
     * @return mixed
     */
    public function setCC($m, $data)
    {
        if (isset( $data['cc'] )) {
            $m->cc($data['cc']);

            return $m;
        }
    }

    /**
     * @param $m
     * @param $data
     * @return mixed
     */
    public function setBC($m, $data)
    {
        if (isset( $data['bc'] )) {
            $m->cc($data['bc']);

            return $m;
        }
    }


}