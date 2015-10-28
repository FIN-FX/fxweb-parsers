<?php
/* @var $this AutoRiaController */
/* @var $model AutoRia */

$this->breadcrumbs=array(
    'AutoRia'=>array('index'),
    'Create',
);

$this->menu=array(
    array('label'=>'List AutoRia', 'url'=>array('index')),
);
?>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>