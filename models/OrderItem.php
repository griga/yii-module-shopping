<?php

/**
 * This is the model class for table "{{shopping_order_item}}".
 *
 * The followings are the available columns in table '{{shopping_order_item}}':
 * @property string $id
 * @property string $order_id
 *
 * @property string $product_id
 * @property string $uid
 *
 * @property integer $sort
 * @property integer $notify
 *
 * @property string $quantity
 * @property double $price
 * @property double $sum
 * @property Product $product
 * @property Order $order
 */
class OrderItem extends CActiveRecord
{
    const NOT_NOTIFY = 0;
    const NOTIFY = 1;
    const NOTIFIED = 2;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{shopping_order_item}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return [
            ['order_id, product_id, price', 'required'],
            ['sort, notify', 'numerical', 'integerOnly' => true],
            ['uid', 'length', 'max' => 127],
            ['price', 'numerical'],
            ['order_id, product_id, quantity', 'length', 'max' => 10],
            ['id, order_id, product_id, sort, quantity, price', 'safe', 'on' => 'search'],
        ];
    }


    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Order',
            'product_id' => 'Product',
            'sort' => 'Sort',
            'quantity' => 'Количество',
            'price' => 'Цена',
            'printPrice' => 'Цена',
            'printPriceWithDiscount' => 'Цена со скидкой',
            'printSum' => 'Сумма',
            'printSumWithDiscount' => 'Сумма со скидкой',
        ];
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search()
    {
        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id, true);
        $criteria->compare('order_id', $this->order_id, true);
        $criteria->compare('product_id', $this->product_id, true);
        $criteria->compare('sort', $this->sort);
        $criteria->compare('quantity', $this->quantity, true);
        $criteria->compare('price', $this->price);

        return new CActiveDataProvider($this, [
            'criteria' => $criteria,
        ]);
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return OrderItem the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function relations(){
        return [
          'order'=>[ self::BELONGS_TO, 'Order', 'order_id'],
            'product'=>[self::BELONGS_TO, 'Product', 'product_id'],
        ];
    }


    public function getSum(){
        return $this->quantity * $this->price;
    }

    public function getSumWithDiscount(){
        return $this->quantity * ($this->price - $this->getDiscount());
    }

    public function getDiscount(){
        return $this->price * $this->order->discount / 100;
    }
    /*------------------------------------*\
      pdf
    \*------------------------------------*/

    public function getUnit()
    {
        return $this->product ? $this->product->unit : '';
    }

    public function getName()
    {
        return $this->product ? $this->product->name : '';
    }

    public function getPrintPrice()
    {
        return YgStringUtils::asRur($this->price);
    }

    public function getPrintPriceWithDiscount()
    {
        return YgStringUtils::asRur($this->price - $this->getDiscount());
    }

    public function getPrintSum()
    {
        return YgStringUtils::asRur($this->getSum());
    }

    public function getPrintSumWithDiscount()
    {
        return YgStringUtils::asRur($this->getSumWithDiscount());
    }

    public function attributesForGenerator(){
        return ['quantity','price','name','unit','printPrice','printSum','printPriceWithDiscount','printSumWithDiscount'];
    }

}
