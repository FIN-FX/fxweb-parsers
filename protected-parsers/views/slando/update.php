<?php
/* @var $this SlandoController */
/* @var $model Slando */

$this->breadcrumbs=array(
    'Slando'=>array('index'),
    $model->id=>array('view','id'=>$model->id),
    'Update ' . $model->id,
);

$this->menu=array(
    array('label'=>'List Slando', 'url'=>array('index')),
    array('label'=>'Create Slando', 'url'=>array('create')),
    array('label'=>'View Slando', 'url'=>array('view', 'id'=>$model->id)),
);
?>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>