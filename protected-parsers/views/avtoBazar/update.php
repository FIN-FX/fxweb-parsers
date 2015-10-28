<?php
/* @var $this AvtoBazarController */
/* @var $model AvtoBazar */

$this->breadcrumbs=array(
    'AvtoBazar'=>array('index'),
    $model->id=>array('view','id'=>$model->id),
    'Update ' . $model->id,
);

$this->menu=array(
    array('label'=>'List AvtoBazar', 'url'=>array('index')),
    array('label'=>'Create AvtoBazar', 'url'=>array('create')),
    array('label'=>'View AvtoBazar', 'url'=>array('view', 'id'=>$model->id)),
);
?>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>