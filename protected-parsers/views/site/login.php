<?php
    $this->pageTitle = Yii::app()->name . ' - Login';
    $this->breadcrumbs = array('Login');
?>

<div class="form">
<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'=>'login-form',
    'htmlOptions'=>array('class'=>'well'),
    'enableClientValidation'=>true,
    'enableAjaxValidation'=>false,
)); ?>

    <?php echo $form->textFieldRow($model,'username'); ?>
    <?php echo $form->passwordFieldRow($model,'password'); ?>
    <?php echo $form->checkBoxRow($model,'rememberMe'); ?>
    
    <div class="form-actions">
        <?php $this->widget('bootstrap.widgets.TbButton', array(
            'buttonType'=>'submit',
            'type'=>'primary',
            'label'=> 'Login',
        )); ?>
    </div>

<?php $this->endWidget(); ?>

</div><!-- form -->
