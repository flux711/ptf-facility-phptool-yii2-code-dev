<?php

namespace api\modules\facility\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\rest\Controller;
use api\modules\facility\models\FacilityCodePool;
use api\modules\facility\models\FacilityCodePoolForm;
use api\modules\facility\models\FacilityStackDetailForm;
use api\modules\facility\models\FacilityStackImageForm;
use api\modules\facility\models\FacilityStackDetail;
use api\modules\facility\models\FacilityStackImage;

class FacilityController extends Controller
{
	/**
	 * {@inheritdoc}
	 */
	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
				'rules' => [
					// allow everything to users who meet the requirements of the hasAccess() method
					[
						'allow' => true,
						'roles' => ['@'],
						'matchCallback' => function() {
							return self::hasAccess();
						}
					],
					// everything else is denied
				],
			],
			'verbs' => [
				'class' => VerbFilter::className(),
				'actions' => [
					'logout' => ['post'],
				],
			],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function actions()
	{
		return [
			'error' => [
				'class' => 'yii\web\ErrorAction',
			],
			'captcha' => [
				'class' => 'yii\captcha\CaptchaAction',
				'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
			],
		];
	}

	public static function hasAccess()
	{
		return !Yii::$app->user->isGuest && Yii::$app->user->getIdentity()->hasDevelopmentPermission();
	}

	/**
	 * Displays homepage.
	 *
	 * @return mixed
	 */
	public function actionIndex()
	{
		return $this->render('index');
	}

	public function actionStack()
	{
		$payload = [];
		$query = FacilityStackDetail::find();
		$provider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => [
				'pageSize' => 20,
			],
		]);
		$payload['provider'] = $provider;

		return $this->render('stack', $payload);
	}

	public function actionImage()
	{
		$request = Yii::$app->request;
		$id = $request->queryParams['id'];

		$query = FacilityStackImage::find();
		$provider = new ActiveDataProvider([
			'query' => $query->filterWhere(
				['facility_stack_detail_id' => $id]
			),
			'pagination' => [
				'pageSize' => 20,
			],
		]);
		$payload['provider'] = $provider;
		$payload['facility_stack_detail_id'] = $id;

		return $this->render('image', $payload);
	}

	public function actionCodepool()
	{
		$query = FacilityCodePool::find();
		$provider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => [
				'pageSize' => 20,
			],
		]);
		$payload['provider'] = $provider;

		return $this->render('code', $payload);
	}


	public function actionAddStackDetail()
	{
		$model = new FacilityStackDetailForm();
		if ($model->load(Yii::$app->request->post())) {
			$verification = $model->verify();
			if ($verification)
				Yii::$app->session->setFlash('error', $verification);
			if (!$verification and $model->create()) {
				Yii::$app->session->setFlash('success', 'Stack added!');
				return $this->redirect(['/facility/stack']);
			}
		}

		return $this->render('addStackDetail', ['model' => $model]);
	}

	public function actionEditStackDetail()
	{
		$request = Yii::$app->request;
		$id = $request->queryParams['id'];
		$config = FacilityStackDetail::findOne($id);

		$model = new FacilityStackDetailForm();

		if ($request->post('FacilityStackDetailForm') && $model->load($request->post()) && $model->update($config)) {
			Yii::$app->session->setFlash('success', 'Stack updated!');
			return $this->redirect(['/facility/stack']);
		}

		$model->setConfig($config);

		return $this->render('editStackDetail', ['model' => $model]);
	}

	public function actionAddStackImage()
	{
		$request = Yii::$app->request;
		$id = $request->queryParams['id'];
		$model = new FacilityStackImageForm();

		$model->facility_stack_detail_id = $id;
		if ($model->load($request->post())) {
			$verification = $model->verify();
			if ($verification)
				Yii::$app->session->setFlash('error', $verification);
			if (!$verification and $model->create()) {
				Yii::$app->session->setFlash('success', 'Stack image added!');
				return $this->redirect(['/facility/stack/'.$model->facility_stack_detail_id.'/image']);
			}
		}

		return $this->render('addStackImage', ['model' => $model]);
	}

	public function actionEditStackImage()
	{
		$request = Yii::$app->request;
		$id = $request->queryParams['id'];
		$config = FacilityStackImage::findOne($id);

		$model = new FacilityStackImageForm();

		if ($request->post('FacilityStackImageForm') && $model->load($request->post()) && $model->update($config)) {
			Yii::$app->session->setFlash('success', 'Stack image updated!');
			return $this->redirect(['/facility/stack/'.$config->facility_stack_detail_id.'/image']);
		}

		$model->setConfig($config);

		return $this->render('editStackImage', ['model' => $model, 'stackdetail' => $config]);
	}

	public function actionAddCodepool()
	{
		$request = Yii::$app->request;
		$model = new FacilityCodePoolForm();

		if ($model->load($request->post())) {
			$verification = $model->verify();
			if ($verification)
				Yii::$app->session->setFlash('error', $verification);
			if (!$verification and $model->create()) {
				Yii::$app->session->setFlash('success', 'Stack image added!');
				return $this->redirect(['/facility/codepool']);
			}
		}

		return $this->render('addCodepool', ['model' => $model]);
	}

	public function actionEditCodepool()
	{
		$request = Yii::$app->request;
		$id = $request->queryParams['id'];
		$config = FacilityCodePool::findOne($id);

		$model = new FacilityCodePoolForm();

		if ($request->post('FacilityCodePoolForm') && $model->load($request->post()) && $model->update($config)) {
			Yii::$app->session->setFlash('success', 'Code pool updated!');
			return $this->redirect(['/facility/codepool']);
		}

		$model->setConfig($config);

		return $this->render('editCodepool', ['model' => $model]);
	}

}
