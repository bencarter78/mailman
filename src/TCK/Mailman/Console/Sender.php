<?php namespace TCK\Mailman\Console;

use Illuminate\Console\Command;
use TCK\Mailman\Mailers\Mailer;

class MailmanTestCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'mailman:test';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Send a test email.';

	/**
	 * @var SurveyMailer
	 */
	private $mailer;

	/**
	 * Create a new command instance.
	 *
	 * @param TestMailer|SurveyMailer $mailer
	 * @return MailmanTestCommand
	 */
	public function __construct( Mailer $mailer )
	{
		parent::__construct();
		$this->mailer = $mailer;
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$recipient = $this->ask( 'What is the email address of the recipient?' . "\n" );
		$this->mailer->sendTest( $recipient );

		$this->info( 'Email sent' );
	}

}
