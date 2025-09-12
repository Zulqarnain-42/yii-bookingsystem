<?php

class AppointmentController extends Controller
{
    public function actionIndex()
    {
        $criteria = new CDbCriteria();

        // Eager load relations
        $criteria->with = ['user', 'service'];
        $criteria->together = true; // required for joined table search

        // Search filter
        $search = Yii::app()->request->getParam('search');
        if (!empty($search)) {
            $criteria->addSearchCondition('user.username', $search, true, 'OR');
            $criteria->addSearchCondition('service.name', $search, true, 'OR');
            $criteria->addSearchCondition('t.appointment_date', $search, true, 'OR');
            $criteria->addSearchCondition('t.appointment_time', $search, true, 'OR');
        }

        // Pagination
        $count = Appointments::model()->count($criteria);
        $pages = new CPagination($count);
        $pages->pageSize = 10;
        $pages->pageVar = 'page';
        $pages->applyLimit($criteria);

        // Fetch appointments
        $appointments = Appointments::model()->findAll($criteria);

        // Render
        if (Yii::app()->request->isAjaxRequest) {
            $this->renderPartial('_appointments_table', compact('appointments', 'pages', 'search'), false, true);
            Yii::app()->end();
        }

        $this->render('index', compact('appointments', 'pages', 'search'));
    }

    public function actionBook()
    {
        if (Yii::app()->request->isAjaxRequest) {
            $data = json_decode(file_get_contents("php://input"), true);

            $userId = Yii::app()->user->id;
            $serviceId = $data['service_id'];
            $date = $data['date'];
            $time = date("H:i:s", strtotime($data['time']));  // Converts '10:00 AM' to '10:00:00'
            
            if (!$userId || !$serviceId || !$date || !$time) {
                echo CJSON::encode([
                    'success' => false,
                    'message' => 'Missing required data.'
                ]);
                Yii::app()->end();
            }

            // Check if appointment already exists
            $existing = Appointments::model()->findByAttributes([
                'user_id' => $userId,
                'service_id' => $serviceId,
                'appointment_date' => $date,
                'appointment_time' => $time
            ]);

            if ($existing) {
                echo CJSON::encode([
                    'success' => false,
                    'message' => 'You have already booked this slot.'
                ]);
                Yii::app()->end();
            }

            // Create new appointment
            $appointment = new Appointments();
            $appointment->user_id = $userId;
            $appointment->service_id = $serviceId;
            $appointment->appointment_date = $date;
            $appointment->appointment_time = $time;

            if ($appointment->save()) {
                echo CJSON::encode([
                    'success' => true,
                    'message' => 'Appointment booked successfully.'
                ]);
            } else {
                echo CJSON::encode([
                    'success' => false,
                    'message' => 'Failed to book appointment. Please try again.'
                ]);
            }

            Yii::app()->end();
        } else {
            throw new CHttpException(400, 'Invalid request.');
        }
    }

}