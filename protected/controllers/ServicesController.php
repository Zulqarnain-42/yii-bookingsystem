<?php

class ServicesController extends Controller
{
    public function actionIndex()
    {
        $criteria = new CDbCriteria();
        $search = Yii::app()->request->getParam('search');

        if (!empty($search)) {
            $criteria->addSearchCondition('name', $search, true, 'OR');
            $criteria->addSearchCondition('description', $search, true, 'OR');
        }

        $count = Services::model()->count($criteria);
        $pages = new CPagination($count);
        $pages->pageSize = 10;
        $pages->pageVar = 'page';
        $pages->applyLimit($criteria);

        $services = Services::model()->findAll($criteria);

        if (Yii::app()->request->isAjaxRequest) {
            $this->renderPartial('_table', compact('services', 'pages', 'search'), false, true);
            Yii::app()->end();
        }

        $this->render('index', compact('services', 'pages', 'search'));
    }

    public function actionCreate()
    {
        $model = new Services;

        if (isset($_POST['Services'])) {

            $model->attributes = $_POST['Services'];
            $model->user_id = Yii::app()->user->id; 

            if ($model->save()) {
                echo json_encode([
                    'success' => true,
                    'service' => [
                        'id' => $model->id,
                        'name' => $model->name,
                        'description' => $model->description
                    ]
                ]);
                Yii::app()->end();
            } else {
                echo json_encode([
                    'success' => false,
                    'errors' => $model->getErrors()
                ]);
                Yii::app()->end();
            }
        }

        echo json_encode(['success' => false, 'message' => 'No data received.']);
        Yii::app()->end();
    }

    public function actionGet($id)
    {
        $model = Services::model()->findByPk($id);
        if ($model) {
            echo CJSON::encode($model->attributes);
        } else {
            echo CJSON::encode(['error' => 'Not found']);
        }
        Yii::app()->end();
    }

    public function actionUpdate()
    {
        $id = Yii::app()->request->getPost('Services')['id'] ?? null;
        $model = Services::model()->findByPk($id);

        if (!$model) {
            throw new CHttpException(404, 'Service not found.');
        }

        if (isset($_POST['Services'])) {

            $model->attributes = $_POST['Services'];
            $model->user_id = Yii::app()->user->id;

            if ($model->save()) {
                echo CJSON::encode([
                    'success' => true,
                    'service' => [
                        'id' => $model->id,
                        'name' => $model->name,
                        'description' => $model->description,
                    ]
                ]);
                Yii::app()->end();
            } else {
                echo CJSON::encode([
                    'success' => false,
                    'errors' => $model->getErrors(),
                ]);
                Yii::app()->end();
            }
        }

        echo CJSON::encode(['success' => false, 'message' => 'No data received.']);
        Yii::app()->end();
    }

    public function actionDelete()
    {
        if (Yii::app()->request->isPostRequest) {
            $id = Yii::app()->request->getPost('id');
            if ($id !== null) {
                Services::model()->deleteByPk($id);
                echo CJSON::encode(['success' => true]);
                Yii::app()->end();
            } else {
                throw new CHttpException(400, 'Missing ID');
            }
        } else {
            throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
        }
    }

public function actionGetSlots($id)
{
    Yii::app()->controller->layout = false;
    header('Content-Type: application/json');

    $service = Services::model()->findByPk($id);
    $date = Yii::app()->request->getParam('date'); // Format: YYYY-MM-DD

    if (!$service || !$date) {
        echo CJSON::encode(['error' => 'Invalid service or date']);
        Yii::app()->end();
    }

    $start = strtotime($service->start_time);
    $end = strtotime($service->end_time);

    $duration = is_numeric($service->duration)
        ? (int)$service->duration
        : $this->parseDuration($service->duration);

    $slots = [];

    while (($start + $duration * 60) <= $end) {
        $slotTime = date('H:i', $start);
        $displayTime = date('g:i A', $start);

        // Check if this slot is booked on the given date
        $alreadyBooked = Appointments::model()->exists(
            'service_id = :service_id AND appointment_date = :date AND appointment_time = :time',
            [
                ':service_id' => $id,
                ':date' => $date,
                ':time' => $slotTime,
            ]
        );

        $slots[] = [
            'time' => $displayTime,
            'booked' => $alreadyBooked,
        ];

        $start += $duration * 60;
    }

    echo CJSON::encode($slots);
    Yii::app()->end();
}


    
    public function actionview($id)
    {
        $model = Services::model()->with('provider')->findByPk($id);
        if ($model === null) {
            throw new CHttpException(404, 'The requested service does not exist.');
        }

        $this->render('details', [
            'model' => $model,
            'provider' => $model->provider, // pass user info to view
        ]);
    }
}
