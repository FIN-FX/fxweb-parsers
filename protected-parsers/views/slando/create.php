<?php
/* @var $this SlandoController */
/* @var $model Slando */

$this->breadcrumbs=array(
    'Slando'=>array('index'),
    'Create',
);

$this->menu=array(
    array('label'=>'List Slando', 'url'=>array('index')),
);
?>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>