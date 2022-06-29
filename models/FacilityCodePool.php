<?php

namespace flux711\yii2\facility_phptool_code_dev\models;

use api\modules\rhea\common\models\ConnectionManager;
use Yii;
use yii\web\BadRequestHttpException;

class FacilityCodePool
{
	public static function checkCode($code, $pool)
	{
		$url = 'https://phytecphptool.phytec.de/api/v1/number/';
		$url = $url."is-scan-code-valid?ScanCode=".$code."&PoolId=".$pool;

		$result = ConnectionManager::curlOperation($url);
		if ($result['Error'] == true)
			throw new BadRequestHttpException($result['ErrorMessage']);

		return ["valid" => $result['valid']];
	}
}
