<?php
/* @var $this AvtoBazarController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
    'AvtoBazar',
);

$this->menu=array(
    array('label'=>'Create AvtoBazar', 'url'=>array('create')),
);
Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
    $('.search-form').toggle();
    return false;
});
$('.search-form form').submit(function(){
    $('#avto-bazar-grid').yiiGridView('update', {
        data: $(this).serialize()
    });
    return false;
});
");

$this->widget('bootstrap.widgets.TbGridView', array(
    'type' => 'striped bordered condensed',
    'dataProvider' => $dataProvider,
    'filter' => $model,
    'columns' => $columns,
));
?>