<?php

namespace flux711\yii2\facility_phptool_code_dev;

use flux711\yii2\facility_phptool_code_dev\models\FacilityCodePool;

class Module extends \yii\base\Module
{

	public $controllerNamespace = 'flux711\yii2\facility_phptool_code_dev\controllers';

	public function checkCode($code, $pool)
	{
		return FacilityCodePool::checkCode($code, $pool);
	}
}
