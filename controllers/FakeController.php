<?php

namespace api\modules\fake\controllers;

use api\modules\fake\models\FakeCodePool;
use api\modules\fake\models\FakeCodePoolForm;
use api\modules\fake\models\FakeStackDetailForm;
use api\modules\fake\models\FakeStackImageForm;
use api\modules\fake\models\FakeStackDetail;
use api\modules\fake\models\FakeStackImage;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\rest\Controller;

class FakeController extends Controller
{
	public function actionGetStacks()
	{
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

		$fakedetails = FakeStackDetail::find()->all();
		foreach($fakedetails as $fakedetail) {
			$fakedetail['image'] = $fakedetail->image;
		}
		return $fakedetails;
	}

	public function actionGetStackById()
	{
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		$request = Yii::$app->request;

		if (!$request->get('id'))
			throw new \yii\web\BadRequestHttpException("Stack ID is missing!");

		$fakedetail = FakeStackDetail::find()->where([
			'fake_stack_detail_id' => $request->get('id')
		])->one();

		return $this->formatStackData($fakedetail);
	}

	public function actionGetImages()
	{
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

		$fakeimages = FakeStackImage::find()->all();
		return $fakeimages;
	}

	public function actionGetImageById()
	{
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		$request = Yii::$app->request;

		if (!$request->get('id'))
			throw new \yii\web\BadRequestHttpException("Image ID is missing!");

		$fakeimage = FakeStackImage::find()->where([
			'fake_stack_image_id' => $request->get('id')
		])->one();
		return $fakeimage;
	}

	public function actionGetCodepools()
	{
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

		$codepools = FakeCodePool::find()->all();
		return $codepools;
	}

	public function actionGetCodepoolById()
	{
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		$request = Yii::$app->request;

		if (!$request->get('id'))
			throw new \yii\web\BadRequestHttpException("Codepool ID is missing!");

		$codepool = FakeCodePool::find()->where([
			'fake_stack_detail_id' => $request->get('id')
		])->one();
		return $codepool;
	}

	public function actionAddStackDetail()
	{
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		$request = Yii::$app->request;

		if (!$request->post('production_order_id'))
			throw new \yii\web\BadRequestHttpException("Productionorder ID is missing!");
		if (!$request->post('buck_sheet_id'))
			throw new \yii\web\BadRequestHttpException("Bucksheet ID is missing!");
		if (!$request->post('part_number'))
			throw new \yii\web\BadRequestHttpException("Partnumber is missing!");

		$model = new FakeStackDetailForm();

		if ($model->load(Yii::$app->request->post())) {
			$verification = $model->verify();
			if ($verification)
				throw new \yii\web\BadRequestHttpException($verification);
			if (!$verification and $model->create()) {
				Yii::$app->response->statusCode = 201;
				return;
			}
		}
		throw new \yii\web\BadRequestHttpException("Unable to add stack!");
	}

	public function actionEditStackDetail()
	{
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		$request = Yii::$app->request;

		if (!$request->post('id'))
			throw new \yii\web\BadRequestHttpException("Stack ID is missing!");

		$config = FakeStackDetail::findOne($request->post('id'));
		$model = new FakeStackDetailForm();

		if ($model->load($request->post()) && $model->update($config)) {
			Yii::$app->response->statusCode = 200;
			return;
		}
		throw new \yii\web\BadRequestHttpException("Unable to edit stack!");
	}

	public function actionAddStackImage()
	{
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		$request = Yii::$app->request;

		if (!$request->post('id'))
			throw new \yii\web\BadRequestHttpException("Image ID is missing!");
		if (!$request->post('part_number'))
			throw new \yii\web\BadRequestHttpException("Partnumber is missing!");
		if (!$request->post('reference'))
			throw new \yii\web\BadRequestHttpException("Reference is missing!");

		$model = new FakeStackImageForm();
		$model->fake_stack_detail_id = $request->post('id');

		if ($model->load($request->post())) {
			$verification = $model->verify();
			if ($verification)
				throw new \yii\web\BadRequestHttpException($verification);
			if (!$verification and $model->create()) {
				Yii::$app->response->statusCode = 201;
				return;
			}
		}
		throw new \yii\web\BadRequestHttpException("Unable to add image!");
	}

	public function actionEditStackImage()
	{
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		$request = Yii::$app->request;

		if (!$request->post('id'))
			throw new \yii\web\BadRequestHttpException("Image ID is missing!");

		$config = FakeStackImage::findOne($request->post('id'));
		$model = new FakeStackImageForm();

		if ($model->load($request->post()) && $model->update($config)) {
			Yii::$app->response->statusCode = 200;
			return;
		}
		throw new \yii\web\BadRequestHttpException("Unable to edit image!");
	}

	public function actionAddCodepool()
	{
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		$request = Yii::$app->request;

		if (!$request->post('name'))
			throw new \yii\web\BadRequestHttpException("Name is missing!");
		if (!$request->post('regex'))
			throw new \yii\web\BadRequestHttpException("Regex is missing!");

		$model = new FakeCodePoolForm();

		if ($model->load($request->post())) {
			$verification = $model->verify();
			if ($verification)
				throw new \yii\web\BadRequestHttpException($verification);
			if (!$verification and $model->create()) {
				Yii::$app->response->statusCode = 201;
				return;
			}
		}
		throw new \yii\web\BadRequestHttpException("Unable to add codepool!");
	}

	public function actionEditCodepool()
	{
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		$request = Yii::$app->request;

		if (!$request->post('id'))
			throw new \yii\web\BadRequestHttpException("Codepool ID is missing!");

		$config = FakeCodePool::findOne($request->post('id'));
		$model = new FakeCodePoolForm();

		if ($request->post('FakeCodePoolForm') && $model->load($request->post()) && $model->update($config)) {
			Yii::$app->response->statusCode = 200;
			return;
		}
		throw new \yii\web\BadRequestHttpException("Unable to edit codepool!");
	}

	private function formatStackData($stack)
	{
		$images = [];
		foreach($stack->image as $image) {
			array_push($images, $image);
		}

		$stack = array($stack);
		$stack['images'] = [];
		//array_merge($stack['images'], $images);
		return $stack;
	}

}