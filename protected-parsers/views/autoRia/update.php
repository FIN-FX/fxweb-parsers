<?php
/* @var $this AutoRiaController */
/* @var $model AutoRia */

$this->breadcrumbs=array(
    'AutoRia'=>array('index'),
    $model->id=>array('view','id'=>$model->id),
    'Update ' . $model->id,
);

$this->menu=array(
    array('label'=>'List AutoRia', 'url'=>array('index')),
    array('label'=>'Create AutoRia', 'url'=>array('create')),
    array('label'=>'View AutoRia', 'url'=>array('view', 'id'=>$model->id)),
);
?>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>