<?php
/* @var $this AutoRiaController */
/* @var $model AutoRia */

$this->breadcrumbs=array(
    'AutoRia'=>array('index'),
    $model->id,
);

$this->menu=array(
    array('label'=>'List AutoRia', 'url'=>array('index')),
    array('label'=>'Create AutoRia', 'url'=>array('create')),
    array('label'=>'Update AutoRia', 'url'=>array('update', 'id'=>$model->id)),
    array('label'=>'Delete AutoRia', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
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
