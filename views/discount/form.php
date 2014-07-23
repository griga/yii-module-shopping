<?php /** @var $form TbActiveForm */
/** @var Discount $model */
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id' => 'discount-form',
    'type' => 'horizontal',
    'enableAjaxValidation' => false,
    'clientOptions' => array(
        'validateOnSubmit' => true,
    ),
)); ?>

<div class="row-fluid" id="errors-row">
    <div class="span8 offset2">
        <?php echo $form->errorSummary($model); ?>
    </div>
</div>

<div class="row-fluid">
    <div class="span8 offset2">
        <div class="well">
            <h4><?php echo($model->isNewRecord ? 'Новая скидка' : 'Редактирование скидки'); ?></h4>
            <?php echo $form->textFieldRow($model, 'min_limit', array('class' => 'span6')); ?>
            <?php echo $form->textFieldRow($model, 'max_limit', array('class' => 'span6')); ?>
            <?php echo $form->textFieldRow($model, 'percentage', array('class' => 'span6')); ?>
            <?php echo $form->checkBoxRow($model, 'status'); ?>
            <?php echo $form->hiddenField($model,'user_id') ;?>
        </div>
    </div>
</div>
<div class="row-fluid">
    <div class="span8 offset2">
        <div class="form-actions">
            <?php $this->widget('bootstrap.widgets.TbButton', array(
                'buttonType' => 'submit',
                'type' => 'primary',
                'label' => $model->isNewRecord ? Yii::t('news', 'Создать') : Yii::t('news', 'Сохранить'),
            )); ?>
        </div>
    </div>
</div>
<?php $this->endWidget(); ?>
