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


}