<?php
/* @var $this AvtoBazarController */
/* @var $model AvtoBazar */

$this->breadcrumbs=array(
    'AvtoBazar'=>array('index'),
    $model->id,
);

$this->menu=array(
    array('label'=>'List AvtoBazar', 'url'=>array('index')),
    array('label'=>'Create AvtoBazar', 'url'=>array('create')),
    array('label'=>'Update AvtoBazar', 'url'=>array('update', 'id'=>$model->id)),
    array('label'=>'Delete AvtoBazar', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
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
