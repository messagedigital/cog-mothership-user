<?php

namespace Message\Mothership\User\Task\Porting;

use Message\Cog\Console\Task\Task as BaseTask;
use Message\Cog\DB\Adapter\MySQLi\Connection;
use Symfony\Component\Console\Input\InputArgument;

class EmailSubscriptions extends BaseTask
{

	protected function configure()
	{
		$this
			->addArgument(
				'old',
				InputArgument::REQUIRED,
				'please pass in the name of the service as the last parameter'
			);
	}

	/**
	 * Gets the DB connection to port the data from
	 *
	 * @return Connection 		instance of the DB Connection
	 */
	public function getFromConnection()
	{
		$serviceName = $this->getRawInput()->getArgument('old');
		$service = $this->get($serviceName);

		if (!$service instanceof Connection) {
			throw new \Exception('service must be instance of Message\Cog\DB\Adapter\MySQLi\Connection');
		}

		return $service;
	}

	/**
	 * Gets the DB connection to port the data into
	 *
	 * @return Connection 		instance of the DB Connection
	 */
	public function getToConnection()
	{
		return new \Message\Cog\DB\Adapter\MySQLi\Connection(array(
				'host'		=> $this->get('cfg')->db->hostname,
				'user'		=> $this->get('cfg')->db->user,
				'password' 	=> $this->get('cfg')->db->pass,
				'db'		=> $this->get('cfg')->db->name,
				'charset'	=> 'utf-8',
		));
	}

    public function process()
    {
        $uwOld = $this->getFromConnection();
		$uwNew = $this->getToCOnnection();

		$new = new \Message\Cog\DB\Transaction($uwNew);
		$old = new \Message\Cog\DB\Query($uwOld);

		$sql = '
			SELECT
				val_email.email_name AS email,
				att_email_list.email_id AS subscribed,
				att_email_modified.email_modified AS updated_at
			FROM
				val_email
			LEFT JOIN att_email_list USING (email_id)
			LEFT JOIN att_email_modified USING (email_id)
		';

		$result = $old->run($sql);
		$new->add('TRUNCATE email_subscription');
		$output= '';
		foreach($result as $row) {
			$new->add('
				INSERT INTO email_subscription
				(
					email,
					subscribed,
					updated_at
				)
				VALUES
				(
					:email?s,
					:subscribed?s,
					:updatedAt?dn
				)',
				array(
					'email'      => $row->email,
					'subscribed' => (bool) $row->subscribed,
					'updatedAt'  => $row->updated_at ? new \DateTime($row->updated_at) : null,
				)
			);
		}

		if ($new->commit()) {
        	return $this->writeln('<info>Successfully ported email subscriptions</info>');
		}

		return true;
    }
}