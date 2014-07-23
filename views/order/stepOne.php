<?php
/** Created by griga at 21.11.13 | 19:36.
 * @var Order $model
 */
?>
<div class="content">
    <div class="newOrd">
        <h1>Оформление заказа</h1>

        <a href="/shopping/order/stepOne" class="step stepCur">Шаг 1: Выберите тип оплаты</a>
        <a href="/shopping/order/stepTwo" class="step">Шаг 2: Подтвердите данные</a>
        <a href="/shopping/order/stepThree" class="step stepLast">Шаг 3: Данные доставки</a>

        <form id="step-one" method="POST">
            <div class="paySelect">
                <label>Я оплачиваю как:</label>
                <?php if(SiteVersionSplit::isRozn() && empty($model->payer_type)) $model->payer_type = Order::PAYER_PHYSICAL; ?>
                <?php echo CHtml::activeDropDownList($model, 'payer_type', Order::getPayerTypes(), array('style'=>'width:auto;', 'id' => 'payer_type')); ?>
            </div>

            <!--method payment-->
            <div class="payMethod">
                <?php if(SiteVersionSplit::isRozn() && empty($model->payment_type)) $model->payment_type = Order::PAYMENT_IN_CASH; ?>
                <p><label><input type="radio" <?php echo $model->payment_type == Order::PAYMENT_CASHLESS ? 'checked="checked"' : '' ;?> value="<?php echo Order::PAYMENT_CASHLESS; ?>"
                                 name="Order[payment_type]"/>Безналичный расчёт</label>
                    <span>Выберите способы оплаты</span>
                </p>

                <p><label><input type="radio" <?php echo $model->payment_type == Order::PAYMENT_IN_CASH ? 'checked="checked"' : '' ;?> value="<?php echo Order::PAYMENT_IN_CASH; ?>" name="Order[payment_type]"/>Наличными/
                        Денежный перевод</label>
                    <span>Описание способа оплаты наличными, как будет происходить оплата, и каким образом произведется доставка.</span>
                </p>
            </div>
        </form>
        <!--//method payment-->

        <p class="needPay">К оплате: <span><?php echo app()->shoppingCart->getCostWithDiscount(); ?> р.</span></p>
    </div>
    <a href="<?php echo '/shopping/cart'; ?>" class="greenBtn bottBtn">НАЗАД</a>
    <a href="#" id="submit-step-one"  class="greenBtn bottBtn bottBtnR">ДАЛЕЕ</a>
</div>

<script type="text/javascript">
    $(function(){
        function setAvailablePaymentOptions()
        {
            var physicalPayer = <?php echo Order::PAYER_PHYSICAL; ?>;
            var cashlessPayment = <?php echo Order::PAYMENT_CASHLESS; ?>;
            var cashlessRadio = $('input[name="Order[payment_type]"][value="' + cashlessPayment + '"]');

            if ($('#payer_type').val() == physicalPayer) {
                if (cashlessRadio.is(':checked')) {
                    cashlessRadio.prop('checked', false);
                    $('input[name="Order[payment_type]"][value!="' + cashlessPayment + '"]')
                        .first()
                        .prop('checked', true);
                }
                cashlessRadio.prop('disabled', true);
            }
            else {
                cashlessRadio.prop('disabled', false);
            }
        }

        $('#payer_type').change(setAvailablePaymentOptions);

        setAvailablePaymentOptions();

        $('#submit-step-one').on('click',function(e){
            $('#step-one').submit();
            e.preventDefault();
        });
    });
</script>
