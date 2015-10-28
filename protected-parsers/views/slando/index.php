<?php
/* @var $this SlandoController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
    'Slando',
);

$this->menu=array(
    array('label'=>'Create Slando', 'url'=>array('create')),
);
Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
    $('.search-form').toggle();
    return false;
});
$('.search-form form').submit(function(){
    $('#slando-grid').yiiGridView('update', {
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
