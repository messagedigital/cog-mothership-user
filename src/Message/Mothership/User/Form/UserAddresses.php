<?php

namespace Message\Mothership\User\Form;

use Message\Cog\Form\Handler;
use Message\Cog\Service\Container;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Message\User\UserInterface;
use Message\Mothership\Commerce\Address\Address;

class UserAddresses extends Handler
{

	public function __construct(Container $container)
	{
		parent::__construct($container);
	}

	public function buildForm(UserInterface $user, Address $address = null, $type, $action = '')
	{
		$defaults = array();
		if (!is_null($address)) {
			$defaults = array(
				'address_line_1' => $address->lines[1],
				'address_line_2' => $address->lines[2],
				'address_line_3' => $address->lines[3],
				'address_line_4' => $address->lines[4],
				'town'           => $address->town,
				'postcode'       => $address->postcode,
				'state_id'       => $address->stateID,
				'country_id'     => $address->countryID,
				'telephone'		 => $address->telephone,
			);
		}

		$this->setName($type)
			->setMethod('POST')
			->setDefaultValues($defaults)
			->setAction($action);

		$this->add('address_line_1','text','');
		$this->add('address_line_2','text','')
			->val()->optional();
		$this->add('address_line_3','text','')
			->val()->optional();
		$this->add('address_line_4','text','')
			->val()->optional();
		$this->add('town','text','');
		$this->add('postcode','text','');
		$this->add('state_id','text','State')
			->val()->optional();
		$this->add('country_id', new \Message\Cog\Location\CountryType(), 'Country');
		$this->add('telephone','text','');

		return $this;
	}

}