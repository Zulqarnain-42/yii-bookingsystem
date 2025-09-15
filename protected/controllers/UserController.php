<?php

class UserController extends Controller
{
    public function actionIndex()
    {
        $criteria = new CDbCriteria();

        // ✅ Search filter
        $search = Yii::app()->request->getParam('search');
        if (!empty($search)) {
            $criteria->addSearchCondition('username', $search, true, 'OR');
            $criteria->addSearchCondition('email', $search, true, 'OR');
            $criteria->addSearchCondition('full_name', $search, true, 'OR');
        }

        // ✅ Pagination setup
        $count = Users::model()->count($criteria);
        $pages = new CPagination($count);
        $pages->pageSize = 10;
        $pages->pageVar = 'page'; // Optional but useful

        $pages->applyLimit($criteria);
        $users = Users::model()->findAll($criteria);

        // ✅ AJAX support for pagination/search
        if (Yii::app()->request->isAjaxRequest) {
            $this->renderPartial('_table', [
                'users' => $users,
                'pages' => $pages,
                'search' => $search, // Pass to retain input
            ], false, true);
            Yii::app()->end(); // Don't render layout
        }

        // ✅ Default rendering (initial page load)
        $this->render('index', [
            'users' => $users,
            'pages' => $pages,
            'search' => $search,
        ]);
    }

    public function actionProfile()
    {
        if (Yii::app()->user->isGuest) {
            $this->redirect(['site/login']);
        }

        $this->render('profile');
    }

    public function actionUpdateProfile()
    {
        if (Yii::app()->user->isGuest) {
            throw new CHttpException(403, 'Not authorized.');
        }

        $user = Users::model()->findByPk(Yii::app()->user->id);
        $data = json_decode(file_get_contents("php://input"), true);

        if ($user && isset($data['phone'])) {
            $user->phone = $data['phone'];
            if ($user->save(false, ['phone'])) {  // Save only the phone field, skipping validation for other fields
                echo CJSON::encode(['success' => true]);
                Yii::app()->end();
            }
        }

        echo CJSON::encode(['success' => false]);
        Yii::app()->end();
    }

    public function actionBlock()
    {
        if (Yii::app()->request->isPostRequest) {
            $userId = Yii::app()->request->getPost('id');
            $action = Yii::app()->request->getPost('action'); // either 'block' or 'unblock'

            $user = Users::model()->findByPk($userId);
            if ($user) {
                // Update the user's is_active status based on the action
                if ($action == 'block') {
                    $user->is_active = 0; // Block the user
                } elseif ($action == 'unblock') {
                    $user->is_active = 1; // Unblock the user
                }

                // Save the user status and return a success response
                if ($user->save(false)) {
                    echo CJSON::encode([
                        'success' => true,
                        'new_status' => $user->is_active // Return the new status (0 or 1)
                    ]);
                } else {
                    echo CJSON::encode(['success' => false, 'error' => 'Failed to update user status']);
                }
            } else {
                echo CJSON::encode(['success' => false, 'error' => 'User not found']);
            }

            Yii::app()->end();
        }
    }

    public function actionMakeStaff()
    {
        if (Yii::app()->request->isPostRequest) {
            $userId = Yii::app()->request->getPost('id');
            $action = Yii::app()->request->getPost('action'); // either 'make_staff' or 'make_user'

            $user = Users::model()->findByPk($userId);
            if ($user) {
                // Update the user's role based on the action
                if ($action == 'make_staff') {
                    $user->role = 'staff'; // Assign 'staff' role
                } elseif ($action == 'make_user') {
                    $user->role = 'user'; // Assign 'user' role
                }

                // Save the user status and return a success response
                if ($user->save(false)) {
                    echo CJSON::encode([
                        'success' => true,
                        'new_role' => $user->role // Return the new role
                    ]);
                } else {
                    echo CJSON::encode(['success' => false, 'error' => 'Failed to update user role']);
                }
            } else {
                echo CJSON::encode(['success' => false, 'error' => 'User not found']);
            }

            Yii::app()->end();
        }
    }

    public function actionUpdateRole()
    {
        if (Yii::app()->request->isPostRequest) {
            $userId = Yii::app()->request->getPost('id');
            $newRole = Yii::app()->request->getPost('role'); // 'staff' or 'user'

            $user = Users::model()->findByPk($userId);
            if ($user) {
                // Update the role of the user
                $user->role = $newRole; // Change user role to staff or user
                
                // Save the user and respond
                if ($user->save(false)) {
                    echo CJSON::encode([
                        'success' => true,
                        'new_role' => $user->role // Return the new role
                    ]);
                } else {
                    echo CJSON::encode(['success' => false, 'error' => 'Failed to save status']);
                }
            } else {
                echo CJSON::encode(['success' => false, 'error' => 'User not found']);
            }

            Yii::app()->end();
        }
    }


    public function actionGetUsers()
    {
        header('Content-Type: application/json');

        $users = Users::model()->findAll(array(
            'select' => 'id, username',
            'order' => 'username ASC',
        ));

        $result = array();
        foreach ($users as $user) {
            $result[] = array(
                'id' => $user->id,
                'username' => $user->username,
            );
        }

        echo CJSON::encode($result);
        Yii::app()->end();
    }
}