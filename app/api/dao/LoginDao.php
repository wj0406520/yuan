<?php

namespace app\api\dao;

/**
*
*/
class LoginDao extends AllDao
{
	public function __construct()
	{

	}
	public function checkToken($token)
	{
		return 1;
	}
}