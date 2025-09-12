<?php
/* @var $this SiteController */
/* @var $model LoginForm */
/* @var $form CActiveForm */

$this->pageTitle = Yii::app()->name . ' - Register';
$this->breadcrumbs = array('Register');
?>

<div class="flex items-center justify-center bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
  <div class="max-w-md w-full bg-white p-8 rounded-lg shadow-lg">
    <h1 class="text-3xl font-bold text-center text-gray-800 mb-6">Register</h1>

    <p class="text-sm text-center text-gray-600 mb-6">Please enter your registration details.</p>
    <?php if (Yii::app()->user->hasFlash('success')): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?php echo Yii::app()->user->getFlash('success'); ?>
        </div>
    <?php endif; ?>

    <?php if (Yii::app()->user->hasFlash('error')): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?php echo Yii::app()->user->getFlash('error'); ?>
        </div>
    <?php endif; ?>

    <?php $form = $this->beginWidget('CActiveForm', array(
      'id' => 'register-form',
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
        <?php echo $form->labelEx($model, 'email', ['class' => 'block text-gray-700 font-medium mb-2']); ?>
        <?php echo $form->textField($model, 'email', ['class' => 'w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500']); ?>
        <div class="text-red-600 text-sm mt-1">
          <?php echo $form->error($model, 'email'); ?>
        </div>
      </div>

      <div class="mb-4">
        <?php echo $form->labelEx($model, 'full_name', ['class' => 'block text-gray-700 font-medium mb-2']); ?>
        <?php echo $form->textField($model, 'full_name', ['class' => 'w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500']); ?>
        <div class="text-red-600 text-sm mt-1">
          <?php echo $form->error($model, 'full_name'); ?>
        </div>
      </div>

      <div class="mb-4">
        <?php echo $form->labelEx($model, 'password', ['class' => 'block text-gray-700 font-medium mb-2']); ?>
        <?php echo $form->passwordField($model, 'password', ['class' => 'w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500']); ?>
        <div class="text-red-600 text-sm mt-1">
          <?php echo $form->error($model, 'password'); ?>
        </div>
      </div>

      <div class="mb-4">
        <?php echo $form->labelEx($model, 'repeat_password', ['class' => 'block text-gray-700 font-medium mb-2']); ?>
        <?php echo $form->passwordField($model, 'repeat_password', ['class' => 'w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500']); ?>
        <div class="text-red-600 text-sm mt-1">
          <?php echo $form->error($model, 'repeat_password'); ?>
        </div>
      </div>

      <div>
        <?php echo CHtml::submitButton('Register', [
          'class' => 'w-full bg-indigo-600 text-white py-2 rounded-lg font-semibold hover:bg-indigo-700 transition duration-200'
        ]); ?>
      </div>

    <?php $this->endWidget(); ?>
  </div>
</div>
