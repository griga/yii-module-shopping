<?php
/** Created by griga at 07.07.2014 | 14:33.
 * 
 */

class CheckoutForm extends CFormModel {

    public $firstname;
    public $lastname;
    public $address;
    public $phone;
    public $email;

    public function rules()
    {
        return [
            ['firstname, lastname, address, phone, ', 'required'],
            ['firstname, lastname, address, phone', 'length', 'max' => 255],
            ['email','email'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'firstname'=>ts('First Name'),
            'lastname'=>ts('Last Name'),
            'address'=>ts('Address'),
            'phone'=>ts('Phone'),
            'email'=>ts('Email'),
        ];
    }


} 