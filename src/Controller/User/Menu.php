<?php

namespace Message\Mothership\User\Controller\User;

use Message\Cog\Controller\Controller;

class Menu extends Controller
{
	public function index($userID)
	{
		return $this->render('Message:Mothership:User::user:listing:tabs', array(
			'userID' => $userID,
		));	}
}