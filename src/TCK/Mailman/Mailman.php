<?php
namespace TCK\Mailman;

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
        try {
            $data['user'] = $user;

            $this->mailer->send($view, $data, function ($m) use (
                $subject,
                $data
            ) {
                $this->from($m, $data);
                $this->attach($m, $data);
                $this->cc($m, $data);
                $this->bcc($m, $data);
                $this->addTextHeaders($m, $data);

                return $m->to($this->recipient($data['user']))
                         ->subject($subject);
            });
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @param $data
     * @return mixed
     */
    public function recipient($data)
    {
        return is_object($data) ? $data->email : $data;
    }

    /**
     * @param $m
     * @param $data
     */
    function from($m, $data)
    {
        if (isset($data['overwriteFrom'])) {
            $m->from($data['email'], $this->name($data));
        }
    }

    /**
     * @param      $data
     * @param null $fname
     * @param null $surname
     * @return string
     */
    public function name($data, $fname = null, $surname = null)
    {
        if (isset($data['first_name'])) {
            $fname = $data['first_name'] . ' ';
        }

        if (isset($data['surname'])) {
            $surname = $data['surname'];
        }

        return $fname . $surname;
    }

    /**
     * @param $m
     * @param $data
     * @return mixed
     */
    function attach($m, $data)
    {
        if (isset($data['attachment'])) {
            return $m->attach($data['attachment'], ['mime' => 'application/pdf']);
        }
    }

    /**
     * @param $m
     * @param $data
     * @return mixed
     */
    public function cc($m, $data)
    {
        if (isset($data['cc'])) {
            $m->cc($data['cc']);

            return $m;
        }
    }

    /**
     * @param $m
     * @param $data
     * @return mixed
     */
    public function bcc($m, $data)
    {
        if (isset($data['bcc'])) {
            $m->bcc($data['bcc']);

            return $m;
        }
    }

    /**
     * @param $message
     * @param $data
     * @return mixed
     */
    public function addTextHeaders($message, $data)
    {
        if (isset($data['headers'])) {
            $headers = $message->getHeaders();
            foreach ($data['headers'] as $key => $value) {
                $headers->addTextHeader($key, $value);
            }
        }

        return $message;
    }
}