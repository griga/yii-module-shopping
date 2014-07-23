<h1><?= ts('Cart') ?></h1>
<commerce-cart items="cartItems" message="itemsCountMessage" cost="cost" currency="currency"></commerce-cart>
<hr/>

<table class="table">
    <thead>
    <tr>
        <th></th>
        <th><?= ts('Name') ?></th>
        <th><?= ts('Count') ?></th>
        <th><?= ts('Price') ?></th>
        <th><?= ts('Sum') ?></th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    <tr ng-repeat="item in cartItems">
        <td>
            <img class="img-responsive cart-image" ng-src="{{item.img}}" alt=""/>
        </td>
        <td>
            <a ng-href="{{item.url}}" ng-bind="item.name"></a>

        </td>
        <td>
            <input type="text" ng-model="item.quantity" ng-change="changeCount(item)" class="cart-item-count" commerce-spinner/>
        </td>
        <td>
            {{item.price | currency}}
        </td>
        <td>
            {{item.sumPrice | currency}}
        </td>
        <td>
            <button commerce-tooltip="<?= ts('Remove product from cart') ?>" ng-click="removeFromCart(item.id)"
                    class="remove-from-cart btn-xs"><i class="icon-trash"></i></button>
        </td>
    </tr>
    </tbody>
    <tfoot>
    <tr>
        <td colspan="3"></td>
        <td><?= ts('Total') ?>:</td>
        <td>{{cost | currency}}</td>
        <td></td>
    </tr>
    <tr class="cart-actions">
        <td colspan="3"></td>
        <td colspan="2">
            <a href="<?= app()->createUrl('shopping/cart/checkout') ?>" class="cart-checkout btn-block"><i class="icon-ok"></i><span><?= ts('Checkout') ?></span></a>
        </td>
        <td></td>
    </tr>
    </tfoot>
</table>