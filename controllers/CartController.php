<?php

class CartController extends FrontendController
{
    public $layout = '//layouts/catalog';

    public function actionIndex()
    {

        $order = new Order();
        if (app()->session['orderId']) {
            $sessionOrder = Order::model()->findByPk(app()->session['orderId']);
            if ($sessionOrder)
                $order = $sessionOrder;
        }

        $this->render('index', [
            'order' => $order,
        ]);
    }

    public function actionCheckout()
    {
        $model = new CheckoutForm();

        if (isset($_POST['ajax']) && $_POST['ajax'] === 'checkout-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }

        if (isset($_POST['CheckoutForm'])) {
            $model->attributes = $_POST['CheckoutForm'];
            if ($model->validate()) {
                app()->shoppingCart->checkout($model);
                user()->setFlash('user.notify', ts('Thank you for your order. You will receive an call confirmation shortly'));
                $this->redirect(app()->homeUrl);
            }
        }

        $this->render('checkout', [
            'model' => $model
        ]);
    }

    /**
     *
     */
    public function actionOrder()
    {
        $order = $this->inputJson();
        $model = new CheckoutForm();
        $model->firstname = $order['firstname'];
        $model->lastname = $order['lastname'];
        $model->address = $order['address'];
        $model->phone = $order['phone'];
        $model->email = isset($order['email']) ? $order['email'] : '';
        if ($model->validate()) {
            app()->shoppingCart->checkout($model);
            $this->renderJson([
                'message' => ts('Thank you for your order. You will receive an call confirmation shortly'),
                'cart' => app()->shoppingCart->frontData
            ]);
        }
    }

    /**
     * @param Product $product
     */
    private function updateOutOfStock($product)
    {
        if ($product->remains == 0) {
            $product->out_of_stock_counter = $product->out_of_stock_counter + 1;
            db()->createCommand()->update('{{product}}', [
                'out_of_stock_counter' => $product->out_of_stock_counter + 1,
            ], 'id=:pid', [':pid' => $product->id]);
        }
    }


    /**
     *
     */
    public function actionPut()
    {
        $data = $this->inputJson();
        /** @var Product $product */
        $product = Product::model()->findByPk($data['id']);
        $this->updateOutOfStock($product);
        $quantity = $data['quantity'];
        $quantity = $quantity > $product->remains ? $product->remains : $quantity;
        app()->shoppingCart->update($product, $quantity);
        $message = "Товар $product->name ($product->article) добавлен в корзину";
        $this->renderJson([
            'message' => $message,
            'quantity' => $quantity,
            'cart' => app()->shoppingCart->frontData
        ]);
    }

    /**
     *
     */
    public function actionRemove()
    {
        $data = $this->inputJson();
        /** @var Product $product */
        app()->shoppingCart->remove('Product' . $data['id']);
        $message = "Товар удален из корзины";
        $this->renderJson([
            'message' => $message,
            'cart' => app()->shoppingCart->frontData
        ]);

    }
}