<?php /* @var $this Controller */ ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="language" content="en" />

    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/style.css" />
    <title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>

<body>

<?php $this->widget('bootstrap.widgets.TbNavbar',array(
    'items'=>array(
        array(
            'class'=>'bootstrap.widgets.TbMenu',
            'items'=>array(
                array(
                    'label'=>Yii::t('app', 'Home'), 
                    'url'=>array('/site/index'),
                    'visible'=>!Yii::app()->user->isGuest
                ),
                array(
                    'label'=>Yii::t('app', 'AutoRia'), 
                    'url'=>array('/autoRia/index'),
                    'visible'=>!Yii::app()->user->isGuest
                ),
                array(
                    'label'=>Yii::t('app', 'Slando'), 
                    'url'=>array('/slando/index'),
                    'visible'=>!Yii::app()->user->isGuest
                ),
                array(
                    'label'=>Yii::t('app', 'AvtoBazar'), 
                    'url'=>array('/avtoBazar/index'),
                    'visible'=>!Yii::app()->user->isGuest
                ),
                array(
                    'url'=>array('/site/login'), 
                    'label'=>Yii::t('app', 'Login'),
                    'itemOptions' => (
                        (Yii::app()->controller->getId() == 'login') ? 
                        array('class' => 'active') : 
                        array()
                    ),
                    'visible'=>Yii::app()->user->isGuest
                ),
                array(
                    'url'=>array('/site/logout'), 
                    'label'=>Yii::t('app', 'Logout').' ('.Yii::app()->user->name.')', 
                    'itemOptions' => (
                        (Yii::app()->controller->getId() == 'logout') ? 
                        array('class' => 'active') : 
                        array()
                    ),
                    'visible'=>!Yii::app()->user->isGuest
                ),
            ),
        ),
    ),
)); ?>

<div class="container" id="page">

    <?php if(isset($this->breadcrumbs)):?>
        <?php $this->widget('bootstrap.widgets.TbBreadcrumbs', array(
            'links'=>$this->breadcrumbs,
        )); ?><!-- breadcrumbs -->
    <?php endif?>

    <?php echo $content; ?>
    <div class="clear"></div>

    <div id="footer">
    </div><!-- footer -->

</div><!-- page -->

</body>
</html>
