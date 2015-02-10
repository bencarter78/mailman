<?php namespace TCK\Mailman\Mailers;

use TCK\Mailman\Mailman;

class TestMailer extends Mailman {

	/**
	 * @param        $user
	 * @param string $subject
	 * @param array  $data
	 * @return bool
	 */
	public function sendTest( $user, $subject = 'Test Message', $data = [ ] )
	{
		$view = 'mailman::emails.test';

		return $this->sendTo( $user, $subject, $view, $data );
	}
}