<?php
/** Created by griga at 21.11.13 | 20:30.
 * @var Order $order
 * @var User $user
 * @var Organization $organization
 */
?>
<form id="step-two-form" method="POST">
    <div class="newOrd">
        <h1>Оформление заказа</h1>

        <a href="/shopping/order/stepOne" title="" class="step">Шаг 1: Выберите тип оплаты</a>
        <a href="/shopping/order/stepTwo" title="" class="step stepCur">Шаг 2: Подтвердите данные</a>
        <a href="/shopping/order/stepThree" title="" class="step stepLast">Шаг 3: Данные доставки</a>

        <?php if ($user->hasErrors()): ?>
            <div class="personD">
                <div class="flash-error row"><?= CHtml::errorSummary($user); ?></div>
            </div>
        <?php endif; ?>


        <!--person data-->
        <div class="row">
            <div class="col-xs-12">
                <div class="personD">
                    <h4>Личные данные</h4>

                    <?php if (user()->isGuest): ?>
                        <!--person data-->
                        <p><label><?= CHtml::activeLabelEx($user, 'email') ?>: </label><?= CHtml::activeTextField($user, 'email'); ?></p>
                    <?php endif; ?>

                    <p><label><?= CHtml::activeLabelEx($user, 'first_name') ?>: </label><?= CHtml::activeTextField($user, 'first_name'); ?></p>

                    <p><label><?= CHtml::activeLabelEx($user, 'last_name') ?>: </label><?= CHtml::activeTextField($user, 'last_name'); ?></p>

                    <?php if (user()->isGuest): ?>
                    <div id="email-note">E-mail требуется только для подтверждения Вашего заказа и не будет использоваться для рассылок рекламы.</div>
                    <?php endif; ?>

                    <p><label><?= CHtml::activeLabelEx($user, 'middle_name') ?>: </label><?= CHtml::activeTextField($user, 'middle_name'); ?></p>

                    <p><label><?= CHtml::activeLabelEx($user, 'contact_phone') ?>: </label><?= CHtml::activeTextField($user, 'contact_phone'); ?></p>
                </div>

            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
            <p style="text-indent: 0;font-size: 12px;"><span class="required">*</span> - Поля обязательные для заполнения</p>
            </div>
        </div>
        <!--//person data-->

            <div class="personD">
                <h4>Данные организации</h4>
                <?php $this->renderPartial('personal.views.cabinet.organization', array(
                    'model' => $organization,
                    'order'=> $order,
                )) ?>
            </div>
            <script type="text/javascript">
                $(function () {
                    var $of = $('#organization-form');
                    $of.on('submit', function (e) {
                        $.post('/personal/cabinet/organization', $of.serialize(), function () {
                            $of.find('input[type=submit]').before('<div class="flash-success">Данные сохранены</div>')
                        });
                        e.preventDefault();
                    })
                })
            </script>
    </div>
</form>
<a href="/shopping/order/stepOne" class="greenBtn bottBtn">НАЗАД</a>
<a href="#" id="step-two-submit" class="greenBtn bottBtn bottBtnR">ДАЛЕЕ</a>
<script type="text/javascript">
    $('#step-two-submit').on('click', function (e) {
        $('#step-two-form').submit();
        e.preventDefault()
    })
</script>
