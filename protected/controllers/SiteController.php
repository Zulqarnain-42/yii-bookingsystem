<?php

class SiteController extends Controller
{
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
		$criteria = new CDbCriteria();

		// Total count for pagination
		$count = Services::model()->count($criteria);

		// Setup pagination, 10 records per page
		$pages = new CPagination($count);
		$pages->pageSize = 9;
		$pages->applyLimit($criteria);

		// Fetch services with limit & offset applied
		$services = Services::model()->findAll($criteria);

		$this->render('index', [
			'services' => $services,
			'pages' => $pages,
		]);
	}

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
		if($error=Yii::app()->errorHandler->error)
		{
			if(Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else
				$this->render('error', $error);
		}
	}

	/**
	 * Displays the contact page
	 */
	public function actionContact()
	{
		$model=new ContactForm;
		if(isset($_POST['ContactForm']))
		{
			$model->attributes=$_POST['ContactForm'];
			if($model->validate())
			{
				$name='=?UTF-8?B?'.base64_encode($model->name).'?=';
				$subject='=?UTF-8?B?'.base64_encode($model->subject).'?=';
				$headers="From: $name <{$model->email}>\r\n".
					"Reply-To: {$model->email}\r\n".
					"MIME-Version: 1.0\r\n".
					"Content-Type: text/plain; charset=UTF-8";

				mail(Yii::app()->params['adminEmail'],$subject,$model->body,$headers);
				Yii::app()->user->setFlash('contact','Thank you for contacting us. We will respond to you as soon as possible.');
				$this->refresh();
			}
		}
		$this->render('contact',array('model'=>$model));
	}

	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
		if (!Yii::app()->user->isGuest) {
			$this->redirect(['site/index']);
		}

		$model=new LoginForm;

		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if(isset($_POST['LoginForm']))
		{
			$model->attributes=$_POST['LoginForm'];
			// validate user input and redirect to the previous page if valid
			if($model->validate() && $model->login())
				$this->redirect(Yii::app()->user->returnUrl);
		}
		// display the login form
		$this->render('login',array('model'=>$model));
	}

	public function actionRegister()
	{
		if (!Yii::app()->user->isGuest && !Yii::app()->user->is_admin) {
			$this->redirect(array('site/index'));
		}

		$model = new Users;

		if (isset($_POST['Users'])) {
			$model->attributes = $_POST['Users'];

			// Hash the raw password
			$model->password_hash = password_hash($_POST['Users']['password'], PASSWORD_BCRYPT);

			if ($model->save()) {
				Yii::app()->user->setFlash('success', 'Registration successful!');
				$this->refresh();
			} else {
				// Show validation errors
				print_r($model->getErrors());
				Yii::app()->end();
			}
		}

		$this->render('register', array('model' => $model));
	}

	public function actionIsLoggedIn()
	{
		echo CJSON::encode([
			'loggedIn' => !Yii::app()->user->isGuest
		]);
		Yii::app()->end();
	}

	public function actionAppointmentStats() {
		$userId = Yii::app()->user->id;
		$isAdmin = Yii::app()->user->role === 'admin';

		// Date ranges
		$today      = date('Y-m-d');
		$monday     = date('Y-m-d', strtotime('monday this week'));
		$sunday     = date('Y-m-d', strtotime('sunday this week'));
		$firstDay   = date('Y-m-01');
		$lastDay    = date('Y-m-t');

		// Get count of pending appointments
		$countToday = $this->getCount($today, $today, $userId, $isAdmin);
		$countWeek  = $this->getCount($monday, $sunday, $userId, $isAdmin);
		$countMonth = $this->getCount($firstDay, $lastDay, $userId, $isAdmin);
		$countCompleted = $this->getCompleted($firstDay, $lastDay, $userId, $isAdmin);
		$countCancelled = $this->getcanceled($firstDay, $lastDay, $userId, $isAdmin);

		// Output as JSON
		header('Content-Type: application/json');
		echo CJSON::encode([
			'today' => (int)$countToday,
			'week'  => (int)$countWeek,
			'month' => (int)$countMonth,
			'completed' => (int)$countCompleted,
			'cancelled' => (int)$countCancelled,
		]);
		Yii::app()->end();
	}

	function getCompleted($startDate, $endDate, $userId = null, $isAdmin = false){
		// Create a new criteria for the query
		$criteria = new CDbCriteria();
		
		// Filter appointments by date range
		$criteria->addBetweenCondition('appointment_date', $startDate, $endDate);

		// Only count appointments that are "Completed"
		$criteria->compare('appointment_status', 'completed');
		
		// If not admin, restrict to the logged-in user's appointments
		if (!$isAdmin) {
			$criteria->compare('user_id', $userId);
		}
		
		// Count the number of records matching the criteria
		return Appointments::model()->count($criteria);
	}

	function getcanceled($startDate, $endDate, $userId = null, $isAdmin = false){
		// Create a new criteria for the query
		$criteria = new CDbCriteria();
		
		// Filter appointments by date range
		$criteria->addBetweenCondition('appointment_date', $startDate, $endDate);

		// Only count appointments that are "Cancelled"
		$criteria->compare('appointment_status', 'cancelled');

		// If not admin, restrict to the logged-in user's appointments
		if (!$isAdmin) {
			$criteria->compare('user_id', $userId);
		}
		
		// Count the number of records matching the criteria
		return Appointments::model()->count($criteria);
	}

	function getCount($startDate, $endDate, $userId = null, $isAdmin = false) {
		// Create a new criteria for the query
		$criteria = new CDbCriteria();
		
		// Filter appointments by date range
		$criteria->addBetweenCondition('appointment_date', $startDate, $endDate);
		
		// Only count appointments that are "Pending"
		$criteria->compare('appointment_status', 'Pending');
		
		// If not admin, restrict to the logged-in user's appointments
		if (!$isAdmin) {
			$criteria->compare('user_id', $userId);
		}
		
		// Count the number of records matching the criteria
		return Appointments::model()->count($criteria);
	}


	public function actionDashboard()
	{
		$this->render('dashboard');
	}


	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}
}