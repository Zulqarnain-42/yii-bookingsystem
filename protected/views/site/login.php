<?php
/* @var $this SiteController */
/* @var $model LoginForm */
/* @var $form CActiveForm */

$this->pageTitle = Yii::app()->name . ' - Login';
$this->breadcrumbs = array('Login');
?>

<div class="flex items-center justify-center bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
  <div class="max-w-md w-full bg-white p-8 rounded-lg shadow-lg">
    <h1 class="text-3xl font-bold text-center text-gray-800 mb-6">Login</h1>

    <p class="text-sm text-center text-gray-600 mb-6">Please enter your login credentials.</p>

    <?php $form = $this->beginWidget('CActiveForm', array(
      'id' => 'login-form',
      'enableClientValidation' => true,
      'clientOptions' => array(
        'validateOnSubmit' => true,
      ),
    )); ?>

      <div class="mb-4">
        <?php echo $form->labelEx($model, 'username', ['class' => 'block text-gray-700 font-medium mb-2']); ?>
        <?php echo $form->textField($model, 'username', ['class' => 'w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500']); ?>
        <div class="text-red-600 text-sm mt-1">
          <?php echo $form->error($model, 'username'); ?>
        </div>
      </div>

      <div class="mb-4">
        <?php echo $form->labelEx($model, 'password', ['class' => 'block text-gray-700 font-medium mb-2']); ?>
        <?php echo $form->passwordField($model, 'password', ['class' => 'w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500']); ?>
        <div class="text-red-600 text-sm mt-1">
          <?php echo $form->error($model, 'password'); ?>
        </div>
        <p class="text-xs text-gray-500 mt-2">
          Hint: You may login with <kbd class="px-1 bg-gray-200 rounded">demo</kbd>/<kbd class="px-1 bg-gray-200 rounded">demo</kbd> or <kbd class="px-1 bg-gray-200 rounded">admin</kbd>/<kbd class="px-1 bg-gray-200 rounded">admin</kbd>.
        </p>
      </div>

      <div class="mb-4 flex items-center">
        <?php echo $form->checkBox($model, 'rememberMe', ['class' => 'mr-2']); ?>
        <?php echo $form->label($model, 'rememberMe', ['class' => 'text-gray-700']); ?>
      </div>

      <div>
        <?php echo CHtml::submitButton('Login', [
          'class' => 'w-full bg-indigo-600 text-white py-2 rounded-lg font-semibold hover:bg-indigo-700 transition duration-200'
        ]); ?>
      </div>

    <?php $this->endWidget(); ?>
  </div>
</div>
