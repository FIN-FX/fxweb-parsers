<?php /* @var $this Controller */ ?>
<?php $this->beginContent('//layouts/main'); ?>
<div class="right-sidebar">
    <div class="span-5 last">
        <div id="sidebar">
        <?php
            $this->widget('bootstrap.widgets.TbMenu', array(
                'items'=> array(
                    array(
                        'label' => 'Operations',
                        'items' => $this->menu,
                    ),
                ),
                'htmlOptions'=>array('class'=>'operations'),
            ));
        ?>
        </div><!-- sidebar -->
    </div>
</div>
<div class="left-sidebar">
    <div id="content">
        <?php echo $content; ?>
    </div><!-- content -->
</div>
<?php $this->endContent(); ?>