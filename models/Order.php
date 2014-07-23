<?php

/**
 * This is the model class for table "{{shopping_order}}".
 *
 * The followings are the available columns in table '{{shopping_order}}':
 * @property string $id
 *
 * @property string $number_order
 * @property string $number_accounting
 * @property string $uid
 * 
 * @property double $sum
 * @property double $discount
 *
 * @property integer $status
 * @property integer $status_accounting
 * @property integer $notify
 * 
 * @property string $user_id
 * @property string $address_id
 * @property string $organization_id
 *
 * @property integer $payment_type
 * @property integer $payer_type
 * @property integer $delivery_type
 *
 * @property string $create_time
 * @property string $update_time
 * @property integer $sort
 * @property string $content
 *
 * @property OrderItem[] $items
 * @property OrderItem[] $sortedItems
 * @property User $user
 */
class Order extends CActiveRecord
{
    const PAYMENT_CASHLESS = 0;
    const PAYMENT_IN_CASH = 1;

    const PAYER_CORPORATE = 0;
    const PAYER_PHYSICAL = 1;
    const PAYER_INDIVIDUAL = 2;

    const STATUS_CREATED = 0;
    const STATUS_NEW = 1;
    const STATUS_RESERVED = 2;
    const STATUS_ARCHIVE = 3;
    const STATUS_SHIPPING = 4;

    const STATUS_ACCOUNTING_NEW = 1;
    const STATUS_ACCOUNTING_PAID = 2;
    const STATUS_ACCOUNTING_SHIPPED = 3;

    const DELIVERY_SELF = 0;
    const DELIVERY_TRANSPORT_COMPANY = 1;
    const DELIVERY_COURIER = 2;

    const NOT_NOTIFY = 0;
    const NOTIFY = 1;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{shopping_order}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('status, status_accounting, sort, payment_type, payer_type, delivery_type, notify', 'numerical', 'integerOnly' => true),
            array('discount', 'numerical'),
            array('number_order, number_accounting', 'length', 'max' => 255),
            array('uid', 'length', 'max' => 127),
            array('user_id, organization_id, address_id', 'length', 'max' => 10),
            array('content', 'safe'),
            array('id, number_order, number_accounting, discount, status, status_accounting, content, user_id, create_time, update_time, sort, payment_type, payer_type, delivery_type, organization_id, address_id', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'items' => array(self::HAS_MANY, 'OrderItem', 'order_id'),
            'user' => array(self::BELONGS_TO, 'User', 'user_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'number_order' => 'Номер счета',
            'number_accounting' => 'Номер счета в 1с',
            'discount' => 'Скидка',
            'status' => 'Статус',
            'status_accounting' => 'Статус в 1C',
            'content' => 'Content',
            'user_id' => 'Контрагент',
            'create_time' => 'Создан',
            'update_time' => 'Update Time',
            'sort' => 'Sort',
            'sum' => 'Сумма',
            'payment_type' => 'Payment Type',
            'payer_type' => 'Payer Type',
            'delivery_type' => 'Delivery Type',
            'organization_id' => 'Organization',
            'address_id' => 'Address',
            'notify' => 'Я хочу получать уведомления о поступлении отсутствующих товаров из моего заказа.',
            'sumProp' => 'Сумма прописью',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     * @param integer $site opt or roznica site
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search($site)
    {
        $criteria = new CDbCriteria;
        $criteria->compare('id', $this->id, true);
        $criteria->compare('number_order', $this->number_order, true);
        $criteria->compare('number_accounting', $this->number_accounting, true);
        $criteria->compare('discount', $this->discount);
        $criteria->compare('status', $this->status);
        $criteria->compare('status_accounting', $this->status_accounting);
        $criteria->compare('content', $this->content, true);
        $criteria->compare('user_id', $this->user_id, true);
        $criteria->compare('create_time', $this->create_time, true);
        $criteria->compare('update_time', $this->update_time, true);
        $criteria->compare('sort', $this->sort);
        $criteria->compare('sum', $this->sum);
        $criteria->compare('payment_type', $this->payment_type);
        $criteria->compare('payer_type', $this->payer_type);
        $criteria->compare('delivery_type', $this->delivery_type);
        $criteria->compare('organization_id', $this->organization_id, true);
        $criteria->compare('address_id', $this->address_id, true);

        $criteria->compare('site', $site);
        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Order the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * return behaviors of component merged with parent component behaviors
     * @return array CBehavior[]
     */

    public function behaviors()
    {
        return CMap::mergeArray(
            parent::behaviors(),
            array(
                'timestamp' => array(
                    'class' => 'zii.behaviors.CTimestampBehavior',
                    'setUpdateOnCreate' => true,
                ),
            ));
    }


    /*------------------------------------*\
      constant lists
    \*------------------------------------*/
    public static function getPaymentTypes()
    {
        return array(
            self::PAYMENT_CASHLESS => 'Безналичный расчёт',
            self::PAYMENT_IN_CASH => 'Наличными/ Денежный перевод',
        );
    }

    public function getPaymentType()
    {
        $types = self::getPaymentTypes();
        return $types[$this->payment_type];
    }

    public static function getPayerTypes()
    {
        return array(
            self::PAYER_CORPORATE => 'Юридическое лицо',
            self::PAYER_INDIVIDUAL => 'Индивидуальный предприниматель',
            self::PAYER_PHYSICAL => 'Физическое лицо',
        );
    }

    public function getPayerType()
    {
        $types = self::getPayerTypes();
        return $types[$this->payer_type];
    }


    protected function afterSave()
    {
        if (!$this->number_order) {

            db()->createCommand()->update($this->tableName(), array(
                'number_order' => 'K-' . $this->id
            ), 'id=:nid', array(':nid' => $this->id));
        }
        parent::afterSave();
    }

    protected function beforeDelete()
    {
        foreach ($this->items as $item)
            $item->delete();

//        OrderExport::exportOrderForDelete($this);

        return parent::beforeDelete();
    }


    /*------------------------------------*\
      admin
    \*------------------------------------*/

    /*------------------------------------*\
  STATUS BADGES
\*------------------------------------*/

    public function getStatusBadges()
    {
        $badges = '';
        switch ($this->status_accounting) {
            case self::STATUS_1C_NEW:
                $badges .= '<span class="badge badge-important">&nbsp;</span>';
                break;
            case self::STATUS_1C_PAID:
                $badges .= '<span class="badge badge-success">&nbsp;</span>';
                break;
            case self::STATUS_1C_SHIPPED:
                $badges .= '<span class="badge badge-info">&nbsp;</span>';
                break;
        }

        switch ($this->status) {
            case self::STATUS_RESERVED:
                $badges .= '<span class="badge badge-inverse">&nbsp;</span>';
                break;
        }

        return $badges;
    }


    /**
     * Возвращает сумму всех позиций счета.
     * @return float
     */
    public function getItemsSum()
    {
        $sum = 0;
        foreach ($this->items as $item)
            $sum += $item->sum;
        return $sum;
    }

    /**
     * Возвращает скидку в денежном формате.
     * @return float
     */
    public function getDiscountAsPrice()
    {
        return $this->itemsSum * $this->discount / 100;
    }


    /**
     * Имеет ли счет скидку
     * @return bool
     */
    public function hasDiscount()
    {
        return $this->discount > 0;
    }

    /**
     * Возвраящает скидку счета в формате '7 607,00 руб.'
     * @return string
     */
    public function getPrintDiscount()
    {
        return YgStringUtils::asRur($this->getDiscountAsPrice());
    }

    /**
     * Возвраящает сумму счета без скидки в формате '7 607,00 руб.'
     * @return string
     */
    public function getPrintSum()
    {
        return YgStringUtils::asRur($this->itemsSum);
    }


    /**
     * Возвраящает дату счета со скидкой в формате '26 декабря 2013 г.'
     * @return string
     */
    public function getPrintDate()
    {
        return app()->dateFormatter->format('dd MMMM yyyy', strtotime($this->update_time));
    }

    /**
     * Возвраящает дату и время счета со скидкой в формате '26 декабря 2013 18:44'
     * @return string
     */
    public function getPrintTime()
    {
        return app()->dateFormatter->format('dd MMMM yyyy HH:mm', strtotime($this->update_time));
    }

    /**
     * Возвраящает итоговую сумму со скидкой в формате '7 607,00 руб.'
     * @return string
     */
    public function getPrintSumWithDiscount()
    {
        return YgStringUtils::asRur($this->itemsSum - $this->getDiscountAsPrice());
    }

    /**
     * Возвраящает НДС в формате '7 607,00 руб.'
     * @return string
     */
    public function getPrintTax(){
        $sum = $this->itemsSum - $this->getDiscountAsPrice();
        $tax = $sum - $sum * 100/ 118;
        return YgStringUtils::asRur($tax);
    }

    /**
     * Возвраящает итоговую сумму со скидкой в прописном формате
     * @return string
     */
    public function getSumProp()
    {
        return YgStringUtils::sumPropCapitalized($this->itemsSum - $this->getDiscountAsPrice());
    }

    /**
     * Возвраящает количество позиций в счете
     * @return integer
     */
    public function getItemsCount()
    {
        $count = 0;
        foreach ($this->items as $item)
            $count += intval($item->amount);
        return $count;
    }

    /**
     * Возвраящает позиции счета с проставленой нумерацией
     * @return OrderItem[]
     */
    public function getSortedItems()
    {
        $out = array();
        foreach ($this->items as $index => $item) {
            $item->sort = $index + 1;
            $out[] = $item;
        }
        return $out;
    }

    /**
     * Возвращает пользовательскую ссылку на данный счет
     * @return string
     */
    public function getUserUrl()
    {
        if ($this->status == self::STATUS_RESERVED) {
            return '/personal/cabinet/reserved?order=' . $this->id;
        } else {
            return '/personal/cabinet/history?order=' . $this->id;
        }
    }


    /**
     * Массив атрибутов, используемых при генерации PDF с участием данной модели
     * @return array
     */
    public function attributesForGenerator()
    {
        return array('number_order', 'number_accounting', 'printSum', 'printTax',
            'printSumWithDiscount', 'sumProp', 'itemsCount', 'sumProp');
    }

    public function getFormedCreateTime()
    {
        $date = strtotime($this->create_time);
        $result = date('d-m-y H:i', $date);
        if (strpos(date('d-m-y'), $result) !== 0) {
            $result = explode(' ', $result);
            $result = $result[0];
        }
        return $result;
    }
}
