<?php
/** Created by griga at 20.11.13 | 15:29.
 * @var Discount $model
 */
?>

<div class="page-content">
    <div class="row">
        <div class="span8 offset2">
            <div class="page-header admin-header">
                <h3>Система скидок</h3>
                <?php $this->widget('bootstrap.widgets.TbButton', array(
                    'label' => Yii::t('translate', 'Добавить скидку'),
                    'size' => 'small',
                    'url' => array('/shopping/discount/create'),
                ));
                ?>
            </div>

            <?php $this->widget('bootstrap.widgets.TbExtendedGridView', array(
                'id'=>'discount-grid',
                'type'=>'striped bordered',
                'dataProvider'=>$model->search(),
                'columns'=>array(
                    'range',
                    'percentage',
                    'statusName',
                    array(
                        'class' => 'bootstrap.widgets.TbButtonColumn',
                        'template' => '{update}{delete}'
                    ),
                ),
            )); ?>
        </div>
    </div>
</div>