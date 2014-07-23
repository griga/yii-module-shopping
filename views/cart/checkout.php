<?php
/** Created by griga at 07.07.2014 | 14:11.
 *
 * @var $this FrontendController
 * @var $form CActiveForm
 * @var $model CheckoutForm
 */
?>
<h1><?= ts('Checkout') ?></h1>
<commerce-cart items="cartItems" message="itemsCountMessage" cost="cost" currency="currency"></commerce-cart>
<hr/>

<div class="row">
    <div class="col-sm-10 col-sm-offset-1">
        <?php $form = $this->beginWidget('CActiveForm', [
            'id' => 'checkout-form',
            'errorMessageCssClass' => 'label label-danger',
            'htmlOptions' => [
                'class' => 'form-horizontal',
            ],
            'enableAjaxValidation'=>true,
            'clientOptions' => [
                'validateOnSubmit' => true,
                'validateOnChange' => false,
                'validateOnType' => false,
            ],
        ]) ?>
        <div class="form-group">

            <?= $form->textField($model, 'name', [
                'class' => 'form-control col-sm-8',
            ]) ?>
            <span class="input-group-addon col-sm-4"><?= $model->getAttributeLabel('name') ?></span>

            <div class="col-sm-8">
                <?= $form->error($model, 'name') ?>
            </div>
        </div>
        <div class="form-group">
            <?= $form->textField($model, 'phone', [
                'class' => 'form-control col-sm-8',
            ]) ?>
            <span class="input-group-addon col-sm-4"><?= $model->getAttributeLabel('phone') ?></span>

            <div class="col-sm-8">
                <?= $form->error($model, 'phone') ?>
            </div>
        </div>
        <div class="form-group">
            <?= $form->textArea($model, 'address', [
                'class' => 'form-control col-sm-8',
            ]) ?>
            <span class="input-group-addon col-sm-4"><?= $model->getAttributeLabel('address') ?></span>

            <div class="col-sm-8">
                <?= $form->error($model, 'address') ?>
            </div>
        </div>

        <input type="submit" value="<?= ts('Checkout') ?>"/>
        <?php $this->endWidget() ?>


    </div>
</div>