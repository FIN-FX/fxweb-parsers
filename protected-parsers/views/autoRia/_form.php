<?php
/* @var $this AutoRiaController */
/* @var $model AutoRia */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'=>'auto-ria-form',
    'htmlOptions'=>array('class'=>'well'),
    'enableClientValidation'=>true,
    'enableAjaxValidation'=>false,
)); ?>

    <p class="note">Fields with <span class="required">*</span> are required.</p>

    <?php echo $form->textAreaRow($model,'uri',array('size'=>60,'maxlength'=>255, 'rows' => 7, 'style' => 'width: 80%;')); ?>
    <?php echo $form->textAreaRow($model,'emails',array('size'=>60,'maxlength'=>255, 'rows' => 7, 'style' => 'width: 80%;')); ?>
    <?php echo $form->checkBoxRow($model,'active'); ?>
    
    <div class="form-actions">
        <?php $this->widget('bootstrap.widgets.TbButton', array(
            'buttonType'=>'submit',
            'type'=>'primary',
            'label'=>$model->isNewRecord ? 'Create' : 'Save',
        )); ?>
    </div>

<?php $this->endWidget(); ?>

</div><!-- form -->