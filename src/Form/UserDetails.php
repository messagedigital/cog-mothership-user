<?php

namespace Message\Mothership\User\Form;

use Message\Cog\Form\Handler;
use Message\Cog\Service\Container;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Message\User\UserInterface;
use Message\Mothership\User\User;


class UserDetails extends Handler
{

	public function __construct(Container $container)
	{
		parent::__construct($container);
	}

	public function buildForm(UserInterface $user, $action = '')
	{

		$defaults = array();
		if (!is_null($user)) {
			$defaults = array(
				'title'    => $user->title,
				'forename' => $user->forename,
				'surname'  => $user->surname,
				'email'    => $user->email,
				'type'     => $this->_container['user.profile.type.loader']->getByUser($user)->getName(),
			);
		}

		$this->setMethod('POST')
			->setDefaultValues($defaults)
			->setAction($action);

		$titles = $this->_container['title.list'];

		$this->add('title','choice','', array(
			'choices'  => $titles
		));

		$this->add('forename','text','');
		$this->add('surname','text','');
		$this->add('email','text','');
		$this->add('type', 'choice', '', [
			'choices' => $this->_getTypeChoices()
		]);

		// $this->add('reset_password', 'checkbox', 'Send Reset Password Email?')
		// 	->val()->optional();

		return $this;

	}

	private function _getTypeChoices()
	{
		$types = $this->_container['user.profile.types'];
		$choices = [];

		foreach ($types as $type) {
			$choices[$type->getName()] = $type->getDisplayName();
		}

		return $choices;
	}

}
