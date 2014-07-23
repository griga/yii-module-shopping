<?php
/** Created by griga at 20.11.13 | 0:56.
 * 
 */

?>
<div class="cart-block">
    <h3><a href="/shopping">Корзина</a></h3>
    <a href="/shopping"><img src="<?= app()->theme->baseUrl ?>/images/shopping-cart-icon.png" width="256"  height="256" alt="" /></a>
    <p>Товар <span><span id="aside-items-count"><?= app()->shoppingCart->getItemsCount() ;?></span> шт.</span></p>
    <p>Сумма <span><span id="aside-cost"><?= app()->shoppingCart->getCostWithDiscount() ;?></span> руб.</span></p>
</div>