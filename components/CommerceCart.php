<?php

/** Created by griga at 06.07.2014 | 18:05.
 *
 */
class CommerceCart extends EShoppingCart
{

    public function getFrontData()
    {
        $cost = app()->shoppingCart->getCost();
//        $quantity = ($product && app()->shoppingCart->contains($product->getId())) ? app()->shoppingCart->itemAt($product->getId())->getQuantity() : 0;
        $percentage = Discount::getPercentageByCost($cost);
        $costWithDiscount = $cost - round($cost * $percentage / 100, 2);

        $out = [
            'itemsCount' => app()->shoppingCart->getItemsCount(),
            'itemsCountMessage' => ts('{n} position|{n} positions|{n} positions', app()->shoppingCart->getItemsCount()),
            'currency' => Config::get('site_currency'),
            'cost' => $cost,
            'percentage' => $percentage,
            'costWithDiscount' => $costWithDiscount,
            'items' => [],
        ];
        foreach (app()->shoppingCart as $position) {
            $out['items'][] = [
                'id' => $position->id,
                'name'=>$position->name,
                'image'=>$position->defaultPicture->filename,
                'url'=>$position->getUrl(),
                'alias'=>$position->alias,
                'quantity' => intval($position->getQuantity()),
                'price'=>$position->getPrice(),
                'sumPrice'=>$position->getSumPrice(),
            ];
        }
        return $out;
    }

    /**
     * @param CheckoutForm $model
     */
    public function checkout($model){

        if(user()->isGuest){
            $user = User::model()->findByEmail($model->email);
            $profile = new Profile();
            if($user){
               if($user->profile)
                   $profile = $user->profile;
            } else {
                $user = new User('checkout');
                $user->first_name = $model->firstname;
                $user->last_name = $model->lastname;
                $user->email = $model->email ?: date('ymd_Hi', time()).'@auto.email';
                $user->save();
                $profile = new Profile();
                $profile->user_id = $user->id;

            }
        } else {
            /** @var User $user */
            $user = User::model()->findByPk(user()->id);
            $user->first_name = $model->firstname;
            $user->last_name = $model->lastname;
            $user->save();
            $profile = $user->profile ?: new Profile();
            $profile->user_id = $user->id;
        }
        $profile->phone = $model->phone;
        $profile->address = $model->address;
        $profile->save();
        $order = new Order();
        $order->user_id = $user->id;
        $order->status = Order::STATUS_NEW;

        $order->save();
        foreach($this as $position){
            $item = new OrderItem();
            $item->order_id = $order->id;
            $item->product_id = $position->id;
            $item->price = $position->getPrice();
            $item->quantity = $position->getQuantity();
            $item->save();
        }
        $this->clear();

        foreach(explode(',',Config::get('order_notify_emails')) as $notifyEmail){
            MailService::send('order_notify_email',[
                'sitename'=>Config::get('site_email_from'),
                'siteurl'=>app()->createAbsoluteUrl('site/index'),

                'fullname'=>$model->firstname . ' ' . $model->lastname,
                'phone'=>$model->phone,
                'address'=>$model->address,
                'email'=>$model->email,

                'items'=>$order->items,
                'total'=>$order->getItemsSum(),
                'printTime'=>$order->getPrintTime(),
            ],trim($notifyEmail));
        }

    }

} 