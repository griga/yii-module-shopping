<?php
/** Created by griga at 21.11.13 | 20:30.
 * @var Order $order
 * @var User $user
 * @var Organization $organization
 * @var CActiveForm $form
 */
$form = $this->beginWidget('CActiveForm', array(
	'id' => 'step-three-form',
));
?>


<div class="newOrd">
    <h1>Оформление заказа</h1>

    <a href="/shopping/order/stepOne" class="step">Шаг 1: Выберите тип оплаты</a>
    <a href="/shopping/order/stepTwo" class="step">Шаг 2: Подтвердите данные</a>
    <a href="/shopping/order/stepThree" class="step stepLast stepCur">Шаг 3: Данные доставки</a>


    <div class="delivery">
        <h3>Данные доставки</h3>
    </div>
    <?php if($order->hasErrors()):?>
        <div class="flash-error"><?php echo CHtml::errorSummary($order);?></div>
    <?php endif;?>
    <!--method delivery-->
    <div class="delivMeth">
        <p>Способ доставки:</p>

        <p>
        <?php if (!empty($exportAddress)) { ?>
            <label><input value="<?php echo Order::DELIVERY_SELF ;?>"
                    type="radio" <?php echo $order->delivery_type == Order::DELIVERY_SELF ? 'checked="checked"' : ''; ?>
                    name="Order[delivery_type]"/>самовывоз</label>
        <?php 
            }
            elseif ($order->delivery_type == Order::DELIVERY_SELF){
                $order->delivery_type = Order::DELIVERY_TRANSPORT_COMPANY;
            }
        ?>
            <label><input value="<?php echo Order::DELIVERY_TRANSPORT_COMPANY ;?>"
                    type="radio" <?php echo $order->delivery_type == Order::DELIVERY_TRANSPORT_COMPANY ? 'checked="checked"' : ''; ?>
                    name="Order[delivery_type]"/>транспортной компанией</label>
        </p>
    </div>

<?php if(count($user->addresses)>0):?>
        <div class="saveAdr">
            <?php echo $form->hiddenField($order, 'address_id'); ?>
            <label><input name="Address[select]" value="saved" id="saved-address" type="radio"/>Сохраненные адреса</label>
            <ul class="addresses-list">
                <?php foreach ($user->addresses as $address): ?>
                    <li><a href="#" data-address-id="<?php echo $address->id; ?>"><?php echo $address->toString(); ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="saveAdr saveAdrR">
            <label><input name="Address[select]" value="new" id="new-address" type="radio"/>Использовать новый адрес</label>
            <input name="Address[address1]" type="text" class="newAdress"/>
        </div>
    <script type="text/javascript">
        $(function () {
            var $al = $('.addresses-list');
            $al.on('click', 'a', function (e) {
                $al.find('li').removeClass('active');
                $(this).closest('li').addClass('active');
                $('input[name="Order[address_id]"]').val($(this).data('addressId'));
                $('#saved-address').attr('checked','checked')
                e.preventDefault();
            });
            $('#new-address').on('click',function(){
                $al.find('li').removeClass('active');
            });
            $('#saved-address').on('click',function(){
                $al.find('li:first-child a').click();
            });
            $al.find('li:first-child a').click();
        });
    </script>
    <?php else: ?>
    <div class="saveAdr saveAdrR">
        <label><input name="Address[select]" value="new" id="new-address" type="hidden"/>Укажите адрес для доставки</label>
        <input name="Address[address1]" type="text" class="newAdress"/>
    </div>
<?php endif;?>
    <div class="noDelivery saveAdr"><?php echo $exportAddress; ?></div>
</div>
<?php $this->endWidget(); ?>
<a href="/shopping/order/stepTwo" title="" class="greenBtn bottBtn">НАЗАД</a>
<a href="#" id="submit-step-three" class="greenBtn bottBtn bottBtnR">ГОТОВО</a>
<script type="text/javascript">$(function(){
        $('#submit-step-three').on('click',function(e){
            $('#step-three-form').submit();
            e.preventDefault();
        });

        function showAddresses() {
            var delivery_type = $('input[name="Order[delivery_type]"]:checked').val();
            if (delivery_type == <?php echo Order::DELIVERY_SELF; ?>) {
                $('.saveAdr:not(.noDelivery)').hide();
                $('.noDelivery').show();
            }
            else {
                $('.saveAdr:not(.noDelivery)').show();
                $('.noDelivery').hide();
            }
        }

        $('input[name="Order[delivery_type]"]').change(showAddresses);
        showAddresses();

    })</script>
