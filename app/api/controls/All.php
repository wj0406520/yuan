<?php

namespace app\api\controls;

use core\yuan\Controls;
use core\yuan\Models;
use core\yuan\Route;
use core\yuan\Config;

class All extends Controls
{

	public function before()
	{

	}

	public function configParam($a)
	{
		return Config::getMore('param.'.$a);
	}

}