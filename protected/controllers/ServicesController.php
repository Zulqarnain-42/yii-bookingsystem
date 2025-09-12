<?php

class ServicesController extends Controller
{
    public function actionIndex()
    {
        $criteria = new CDbCriteria();

        // ✅ Handle search input
        $search = Yii::app()->request->getParam('search');
        if (!empty($search)) {
            $criteria->addSearchCondition('name', $search, true, 'OR');
            $criteria->addSearchCondition('description', $search, true, 'OR');
        }

        $count = Services::model()->count($criteria);
        $pages = new CPagination($count);
        $pages->pageSize = 10;

        // ✅ This is where pageVar belongs
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
            $uploadedFile = CUploadedFile::getInstance($model, 'image');

            if ($uploadedFile !== null) {
                $filename = uniqid() . '.' . $uploadedFile->extensionName;
                $model->image = $filename;
            }

            if ($model->save()) {
                // Save image file after model is saved
                if ($uploadedFile !== null) {
                    $uploadPath = Yii::getPathOfAlias('webroot') . '/uploads/' . $filename;
                    $uploadedFile->saveAs($uploadPath);
                }

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


    public function actionview($id)
    {
        $model = Services::model()->findByPk($id);
        if ($model === null) {
            throw new CHttpException(404, 'The requested service does not exist.');
        }

        $this->render('details', ['model' => $model]);   
    }


    public function actionGet($id)
    {
        $model = Services::model()->findByPk($id);
        if ($model) {
            echo CJSON::encode($model->attributes);
            Yii::app()->end();
        }
        echo CJSON::encode(['error' => 'Not found']);
        Yii::app()->end();
    }

    public function actionUpdate()
    {
        $id = Yii::app()->request->getPost('id');
        $model = Services::model()->findByPk($id);

        if (!$model) {
            throw new CHttpException(404, 'Service not found.');
        }

        $oldImage = $model->image;


        if (isset($_POST['Services'])) {
            $model->attributes = $_POST['Services'];

            $uploadedFile = CUploadedFile::getInstance($model, 'image');

            if ($uploadedFile !== null) {
                // Generate a unique filename for the uploaded image
                $filename = uniqid() . '.' . $uploadedFile->extensionName;
                $model->image = $filename;
            } else {
                // Keep the old image if no new file uploaded
                $model->image = $oldImage;
            }

            if ($model->save()) {
                // Save the uploaded file if available
                if ($uploadedFile !== null) {
                    $uploadPath = Yii::getPathOfAlias('webroot') . '/uploads/' . $filename;
                    $uploadedFile->saveAs($uploadPath);
                }

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
                // Return validation errors if save fails
                echo CJSON::encode([
                    'success' => false,
                    'errors' => $model->getErrors(),
                ]);
                Yii::app()->end();
            }
        }

        // If no POST data or other failure
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

}