<?php

class AppointmentController extends Controller
{
    public function actionIndex()
    {
        $criteria = new CDbCriteria();

        // Eager load relations
        $criteria->with = ['user', 'service'];
        $criteria->together = true;

        // Get current user role and ID
        $user = Yii::app()->user;
        $isAdmin = Yii::app()->user->getState('role') === 'admin'; // assuming 'admin' is the role name
        $currentUserId = $user->id;

        // If not admin, filter appointments by current user
        if (!$isAdmin) {
            $criteria->addCondition('t.user_id = :userId');
            $criteria->params[':userId'] = $currentUserId;
        }

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
            $this->renderPartial('_table', compact('appointments', 'pages', 'search'), false, true);
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


    public function actionUpdate()
    {
        if (Yii::app()->request->isAjaxRequest && Yii::app()->request->isPostRequest) {
            $id = Yii::app()->request->getPost('id');
            $model = Appointments::model()->findByPk($id);
            if ($model === null) {
                echo json_encode(['success' => false, 'message' => 'Appointment not found']);
                Yii::app()->end();
            }

            $model->appointment_date = Yii::app()->request->getPost('appointment_date');
            $model->appointment_time = Yii::app()->request->getPost('appointment_time');
            $model->appointment_status = Yii::app()->request->getPost('status');

            if ($model->save()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to save changes']);
            }
            Yii::app()->end();
        }
    }

    public function actionCancel()
    {
        if (Yii::app()->request->isAjaxRequest && Yii::app()->request->isPostRequest) {
            $json = file_get_contents("php://input");
            $data = json_decode($json, true);

            $id = $data['id'] ?? null;
            $appointment = Appointments::model()->findByPk($id);

            if ($appointment === null) {
                echo json_encode(['success' => false, 'message' => 'Appointment not found.']);
                Yii::app()->end();
            }

            // Update status only, keep record
            $appointment->appointment_status = 'cancelled';

            if ($appointment->save()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update appointment status.']);
            }
            Yii::app()->end();
        }
    }

    public function actionComplete()
    {
        if (Yii::app()->request->isAjaxRequest && Yii::app()->request->isPostRequest) {
            $json = file_get_contents("php://input");
            $data = json_decode($json, true);

            $id = $data['id'] ?? null;

            $appointment = Appointments::model()->findByPk($id);

            if ($appointment === null) {
                echo json_encode(['success' => false, 'message' => 'Appointment not found.']);
                Yii::app()->end();
            }

            // Update status only, keep record
            $appointment->appointment_status = 'completed';

            if ($appointment->save()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update appointment status.']);
            }
            Yii::app()->end();
        }
    }

    public function actionGetAppointments()
    {
        // Assuming the user is authenticated
        $userId = Yii::app()->user->id;

        // Fetch all appointments for the logged-in user
        $appointments = Appointments::model()->findAllByAttributes([
            'user_id' => $userId
        ]);

        $events = [];
        foreach ($appointments as $appointment) {
            // Convert appointment data to FullCalendar format
            $events[] = [
                'title' => $appointment->service->name,
                'start' => $appointment->appointment_date . 'T' . $appointment->appointment_time, // FullCalendar expects this format
                'end' => $appointment->appointment_date . 'T' . $appointment->appointment_time, // Optional: adjust if there's a duration
                'description' => $appointment->notes,
                'status' => ucfirst($appointment->status), // You can include any status or field as needed
            ];
        }

        // Send the response as JSON
        echo CJSON::encode($events);
        Yii::app()->end();
    }


}