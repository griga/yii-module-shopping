<?php
/** Created by griga at 21.11.13 | 18:06.
 *
 */

class OrderController extends Controller
{
    public function filters()
    {
        return array('accessControl');
    }

    public function accessRules()
    {
        return (SiteVersionSplit::isOpt() ? array(
            array('allow',
                'users' => array('@'),
            ),
            array('deny',
                'users' => array('*'),
            ),
        ) : array(
            array('allow',
                'users' => array('*')
            )
        ));
    }

    public function actionCreate()
    {
        $order = new Order();
        if (app()->session['orderId'])
            $order = Order::model()->findByPk(app()->session['orderId']);
        if (isset($_POST['Order'])) {
            if( isset($_POST['SelectedProducts'])){
                foreach(app()->shoppingCart as $key => $position)
                    if(!isset($_POST['SelectedProducts'][$position->id]))
                        app()->shoppingCart->remove($key);
            }
            $order->content = $_POST['Order']['content'];
            $order->notify = $_POST['Order']['notify'];
            $order->user_id = user()->id;
            $order->discount = Discount::getPercentageByCost(app()->shoppingCart->getCost());
            $order->site = SiteVersionSplit::getCurrent();
            $order->sum = app()->shoppingCart->getCost();
            if ($order->save()) {
                $this->saveOrderItems($order);
                app()->session['orderId'] = $order->id;
                $this->redirect('/shopping/order/stepOne');
            } else {
                $this->redirect('/shopping/cart');
            }

        } else {
            $this->redirect('/shopping/cart');
        }
    }

    public function actionStepOne()
    {
        $id = app()->session['orderId'];
        /** @var Order $model */
        $model = Order::model()->findByPk($id);
        if (!$model)
            $this->redirect('/shopping/cart');
        if (isset($_POST['Order'])) {
            $model->payer_type = $_POST['Order']['payer_type'];
            $model->payment_type = $_POST['Order']['payment_type'];
            if ($model->save()) {
                app()->session['stepOneComplete'] = true;
                $this->redirect('/shopping/order/stepTwo');
            }
        }
        $this->render('stepOne', array(
            'model' => $model,
        ));
    }

    public function actionStepTwo()
    {
        $doLogin = false;
        if (!isset(app()->session['stepOneComplete']) || !app()->session['stepOneComplete'])
            $this->redirect('/shopping/order/stepOne');

        $id = app()->session['orderId'];
        /** @var Order $order */
        $order = Order::model()->findByPk($id);
        if (!$order)
            $this->redirect('/shopping/cart');

        if (user()->isGuest) {
            $user = new User(User::SCENARIO_REGISTER_ORDER);
            $organization = new Organization();
        } else {
            $user = User::me();
            $user->setScenario(User::SCENARIO_ORDER);
            $organization = Organization::model()->findByAttributes(array(
                'user_id' => user()->id,
            ));
            if (!$organization) {
                $organization = new Organization();
            }
        }

        if (isset($_POST['User'])) {
            if (isset($_POST['Organization'])) {
                $organization->attributes = $_POST['Organization'];
            }
            if ($user->isNewRecord) {
                $user->email = $_POST['User']['email'];
                if(user()->isGuest && !isset($_POST['User']['password'], $_POST['User']['password_repeat'])){
                    $user->password = User::generateRandomPassword();
                    $user->password_repeat = $user->password;
                } elseif(isset($_POST['User']['password'], $_POST['User']['password_repeat'])){
                    $user->password = $_POST['User']['password'];
                    $user->password_repeat = $_POST['User']['password_repeat'];
                }
                $user->role = User::USER_ROLE_CLIENT;
                $user->type = User::USER_TYPE_CLIENT;
                $user->status = User::STATUS_CONFIRMED;
            }
            $user->first_name = $_POST['User']['first_name'];
            $user->last_name = $_POST['User']['last_name'];
            $user->middle_name = $_POST['User']['middle_name'];
            $user->contact_phone = $_POST['User']['contact_phone'];

            $organization->setScenarioByType($organization->type);

            if ($organization->validate() && $user->validate()) {
                $user->password = User::encrypt($user->password);
                $user->save(false);
                if ($doLogin && user()->isGuest) {
                    $login = new LoginForm();
                    $login->username = $user->email;
                    $login->password = $user->password_repeat;
                    $login->login();
                }
                app()->session['user_id']= $user->id;

                $organization->user_id = $user->id;
                $organization->save(false);
                if ($order->payment_type == Order::PAYMENT_CASHLESS) {
                    $order->organization_id = $organization->id;
                }
                $order->user_id = $user->id;
                $order->save(false);
                app()->session['stepTwoComplete'] = true;

                $this->redirect('/shopping/order/stepThree');
            }


        }
        $this->render('stepTwo', array(
            'order' => $order,
            'organization' => $organization,
            'user' => $user,
        ));
    }

    public function actionStepThree()
    {
        if (!isset(app()->session['stepTwoComplete']) || !app()->session['stepTwoComplete'])
            $this->redirect('/shopping/order/stepTwo');

        $id = app()->session['orderId'];
        /** @var Order $order */
        $order = Order::model()->findByPk($id);
        if (!$order)
            $this->redirect('/shopping/cart');

        if (isset($_POST['Order'])) {
            $order->delivery_type = $_POST['Order']['delivery_type'];

            if (isset($_POST['Address'], $_POST['Address']['select']) && $_POST['Address']['select'] == 'new') {
                $address = new Address();
                $address->address1 = $_POST['Address']['address1'];
                $address->entity_class = 'User';
                $address->entity_id = $this->getUser()->id;
                if ($order->delivery_type != Order::DELIVERY_SELF)
                {
                    if ($address->save()) {
                        $order->address_id = $address->id;
                    } else {
                        $order->addError('address_id', 'Адрес не может быть пустым');
                    }
                }
            } else {
                $order->address_id = $_POST['Order']['address_id'];
            }

            if (!$order->hasErrors() && $order->save()) {
                $order->status = Order::STATUS_NEW;
                $order->save(false);
                $this->clearSession();
                $user = $order->user;
                if (SiteVersionSplit::isOpt()) {
                    app()->getModule('mail')->send($user->email, 'marlin.outbox@mail.ru', 'orderNotificationUserOpt', array(
                        'siteNameLink' => CHtml::link(Yii::app()->params['siteName'], Yii::app()->createAbsoluteUrl('/site/index')),
                        'contact_phone' => $user->contact_phone,
                        'number_order'=> $order->number_order,
                    ));
                } else {
                    app()->getModule('mail')->send($user->email, 'marlin.outbox@mail.ru', 'orderNotificationUserRozn', array(
                        'siteNameLink' => CHtml::link(Yii::app()->params['siteName'], Yii::app()->createAbsoluteUrl('/site/index')),
                        'contact_phone' => $user->contact_phone,
                        'number_order'=> $order->number_order,
                    ));
                }

                foreach (User::model()->findAllByAttributes(array(
                    'role' => 'manager'
                )) as $manager)
                    app()->getModule('mail')->send($manager->email, 'marlin.outbox@mail.ru', 'orderNotificationManager', array(
                        'siteNameLink' => CHtml::link(Yii::app()->params['siteName'], Yii::app()->createAbsoluteUrl('/site/index')),
                        'username' => $user->full_name,
                    ));
                $this->setMessage(Misc::t('Информация'), Misc::t('Спасибо за Ваш заказ. Мы отправили подтверждение на Ваш е-mail.'));
                if (user()->isGuest) {
                    $this->redirect('/site/index');
                } else {
                    $this->redirect('/personal/cabinet/history');
                }
            }
        }


        $this->render('stepThree', array(
            'order' => $order,
            'user' => $this->getUser(),
            'exportAddress' => SiteVersionSplit::isRozn() ? Config::get('exportAddressRozn') : Config::get('exportAddressOpt')
        ));
    }

    public function actionReserve()
    {
        if (isset($_POST['Order'])) {
            $order = $this->getOrder();
            $order->content = $_POST['Order']['content'];
            $order->notify = $_POST['Order']['notify'];
            $order->user_id = user()->id;
            $order->discount = Discount::getPercentageByCost(app()->shoppingCart->getCost());
            $order->sum = app()->shoppingCart->getCost();
            $order->status = Order::STATUS_RESERVED;
            $order->site = SiteVersionSplit::getCurrent();
            if ($order->save()) {
                $this->saveOrderItems($order);
                $this->clearSession();
                $this->redirect('/personal/cabinet/reserved');
            } else {
                $this->redirect('/shopping/cart');
            }
        } else {
            $this->redirect('/shopping/cart');
        }
    }

    public function actionRestore($id)
    {
        /** @var Order $order */
        $order = Order::model()->findByPk($id);
        if ($order) {
            app()->shoppingCart->clear();
            foreach ($order->items as $item) {
                app()->shoppingCart->update($item->getProduct(), $item->amount);
            }
            app()->session['orderId'] = $order->id;
            $this->redirect('/shopping/cart');
        } else {
            throw new CHttpException(404, 'Неправильный запрос');
        }
    }

    public function actionRepeat($id)
    {
        /** @var Order $oldOrder */
        $oldOrder = Order::model()->findByPk($id);
        $order = new Order();
        $order->attributes = $oldOrder->attributes;
        $order->unsetAttributes(array(
            'number_order', 'number_1c', 'discount', 'status', 'create_time', 'update_time', 'sum', 'uid'
        ));

        if ($order->save()) {
            app()->shoppingCart->clear();
            foreach ($oldOrder->items as $item) {
                if (isset($_POST['Item']) && isset($_POST['Item'][$item->id])) {
                    $product = $item->getProduct();
                    app()->shoppingCart->update($product, ($product->remains > $item->amount ? $item->amount : $product->remains));
                }
            }
            app()->session['orderId'] = $order->id;
            $this->redirect('/shopping/cart');
        } else {
            throw new CHttpException(404, 'Неправильный запрос');
        }
    }

    private function clearSession()
    {
        app()->session->remove('orderId');
        app()->session->remove('stepOneComplete');
        app()->session->remove('stepTwoComplete');
        app()->shoppingCart->clear();
        // clear other order atempts tha my cause during session
        foreach (Order::model()->findAllByAttributes(array(
            'status' => Order::STATUS_CREATED,
            'user_id' => $this->getUser()->id,
        )) as $order)
            $order->delete();
    }


    /**
     * @param Order $order
     */
    private function saveOrderItems($order)
    {
        OrderItem::model()->deleteAllByAttributes(array(
            'order_id' => $order->id
        ));
        foreach (app()->shoppingCart as $key => $position) {
            $item = new OrderItem();
            $item->product_id = $position->id;
            $item->uid = $position->uid;
            $item->order_id = $order->id;
            $item->price = $position->getPrice();
            $item->amount = $position->getQuantity();
            if ($position->remains == 0 && $order->notify == OrderItem::NOTIFY) {
                $item->notify = OrderItem::NOTIFY;
            }
            $item->save();
        }
    }

    private function getOrder()
    {
        $order = new Order();
        if (app()->session['orderId']) {
            $sessionOrder = Order::model()->findByPk(app()->session['orderId']);
            if ($sessionOrder)
                $order = $sessionOrder;
        }
        return $order;
    }

    private function getUser(){
        if(user()->isGuest && isset(app()->session['user_id'])){
            $user = User::model()->findByPk(app()->session['user_id']);
        } else {
            $user = User::me();
        }

        return $user;
    }
}
