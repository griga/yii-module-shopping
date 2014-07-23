<?php

/**
 * This is the model class for table "{{shopping_discount}}".
 *
 * The followings are the available columns in table '{{shopping_discount}}':
 * @property string $id
 * @property double $min_limit
 * @property double $max_limit
 * @property double $percentage
 * @property integer $enabled
 * @property string $user_id
 * @property integer $sort
 */
class Discount extends CActiveRecord
{

    public function getRange(){
        return $this->min_limit . ' - ' . $this->max_limit . ' рр.';
    }

    public function toString(){
        return $this->getRange() . ' [ '.$this->percentage.'% ]';
    }



	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{shopping_discount}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('min_limit, max_limit, percentage', 'required'),
			array('enabled, sort', 'numerical', 'integerOnly'=>true),
			array('min_limit, max_limit, percentage', 'numerical'),
            array('min_limit','compare','compareAttribute'=>'max_limit','operator'=>'<','message'=>'Нижний лимит не может превышать верхний'),
            array('max_limit','compare','compareAttribute'=>'min_limit','operator'=>'>','message'=>'Верхний лимит не может быть меньше нижнего'),
			array('user_id', 'length', 'max'=>10),
			array('id, min_limit, max_limit, percentage, enabled, user_id, sort', 'safe', 'on'=>'search'),
		);
	}


	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'range' => 'Диапазон',
			'min_limit' => 'Нижний лимит',
			'max_limit' => 'Верхний лимит',
			'percentage' => 'Процент',
			'enabled' => 'Включена',
			'user_id' => 'Пользователь',
			'sort' => 'Sort',
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
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		$criteria=new CDbCriteria;
        $criteria->addCondition('user_id is NULL OR user_id = 0');

		$criteria->compare('id',$this->id,true);
		$criteria->compare('min_limit',$this->min_limit);
		$criteria->compare('max_limit',$this->max_limit);
		$criteria->compare('percentage',$this->percentage);
		$criteria->compare('enabled',$this->enabled);
		$criteria->compare('user_id',$this->user_id,true);
		$criteria->compare('sort',$this->sort);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Discount the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    public static function getPercentageByCost($cost)
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition('user_id IS NULL OR user_id = 0');
        $criteria->addCondition('enabled = 1');
        if(!user()->isGuest)
            $criteria->addCondition('user_id = ' . user()->id, 'OR');
        /** @var Discount[] $discounts */
        $discounts = Discount::model()->findAll($criteria);

        $isUserDiscount = false;
        $result = 0;
        foreach($discounts as $discount){
            if($discount->inRange($cost)){
                if($discount->user_id){
                    $result = $discount->percentage;
                    $isUserDiscount = true;
                } else {
                    if(!$isUserDiscount)
                        $result = $discount->percentage;
                }
            }
        }
        return $result;

    }

    public function inRange($sum){
        return ($this->min_limit < $sum && $this->max_limit > $sum);
    }
}
