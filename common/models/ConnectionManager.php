<?php

namespace api\modules\rhea\common\models;

use yii\web\NotFoundHttpException;

class ConnectionManager
{
	public static function curlOperation($url, $payload = null, $decode = true)
	{
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_URL, $url);
		if ($payload) {
			curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
		}
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$result = curl_exec($curl);
		$errno = curl_errno($curl);
		$error = curl_error($curl);
		curl_close($curl);
		if ($errno > 0) {
			throw new NotFoundHttpException($error);
		}

		if ($decode) {
			return json_decode($result, true);
		} else {
			return $result;
		}
	}

}
