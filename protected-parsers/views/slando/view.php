<?php
/* @var $this SlandoController */
/* @var $model Slando */

$this->breadcrumbs=array(
    'Slando'=>array('index'),
    $model->id,
);

$this->menu=array(
    array('label'=>'List Slando', 'url'=>array('index')),
    array('label'=>'Create Slando', 'url'=>array('create')),
    array('label'=>'Update Slando', 'url'=>array('update', 'id'=>$model->id)),
    array('label'=>'Delete Slando', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
);
?>

<?php $this->widget('zii.widgets.CDetailView', array(
    'data'=>$model,
    'attributes'=>array(
        'id',
        'uri',
        'emails',
        'active',
    ),
)); ?>
