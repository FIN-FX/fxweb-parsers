<?php
/* @var $this AvtoBazarController */
/* @var $model AvtoBazar */

$this->breadcrumbs=array(
    'AvtoBazar'=>array('index'),
    'Create',
);

$this->menu=array(
    array('label'=>'List AvtoBazar', 'url'=>array('index')),
);
?>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>