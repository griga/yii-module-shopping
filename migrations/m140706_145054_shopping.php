<?php

class m140706_145054_shopping extends DbMigration
{
	public function up()
	{
        if(db()->getSchema()->getTable('{{shopping_discount}}'))
            $this->dropTable('{{shopping_discount}}');
        $this->createTable('{{shopping_discount}}', [
            'id'=>'pk',
            'min_limit'=>'DECIMAL(10,2) NOT NULL DEFAULT 0',
            'max_limit'=>'DECIMAL(10,2) NOT NULL DEFAULT 0',
            'percentage'=>'DECIMAL(10,2) NOT NULL DEFAULT 0',
            'enabled'=>'TINYINT NOT NULL DEFAULT 1',
            'user_id'=>'INT NULL',
            'sort'=>'INT NOT NULL DEFAULT 0',
        ]);

        if(db()->getSchema()->getTable('{{shopping_order}}'))
            $this->dropTable('{{shopping_order}}');
        $this->createTable('{{shopping_order}}', array(
            'id' => 'pk',

            'number_order'=>'VARCHAR(255) NULL DEFAULT NULL',
            'number_accounting'=>'VARCHAR(255) NULL DEFAULT NULL',
            'uid'=>'VARCHAR(255) NULL DEFAULT NULL',

            'discount'=>'DECIMAL(10,2) NOT NULL DEFAULT 0',

            'status' => 'TINYINT NOT NULL DEFAULT 0',
            'status_accounting' => 'TINYINT NOT NULL DEFAULT 0',
            'notify'=>'TINYINT NOT NULL DEFAULT 0',

            'user_id' => 'INT NULL DEFAULT NULL',
            'address_id' => 'INT NULL DEFAULT NULL',
            'organization_id' => 'INT NULL DEFAULT NULL',

            'payment_type' => 'TINYINT NOT NULL DEFAULT 0',
            'payer_type' => 'TINYINT NOT NULL DEFAULT 0',
            'delivery_type' => 'TINYINT NOT NULL DEFAULT 0',

            'create_time'=>'DATETIME',
            'update_time'=>'DATETIME',
            'sort' => 'INT NOT NULL DEFAULT 0',
            'content' => 'TEXT NULL DEFAULT NULL',
        ));

        if(db()->getSchema()->getTable('{{shopping_order_item}}'))
            $this->dropTable('{{shopping_order_item}}');
        $this->createTable('{{shopping_order_item}}', array(
            'id' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
            'uid'=>'VARCHAR(255) NULL DEFAULT NULL',
            'order_id' => 'INT NOT NULL',
            'product_id' => 'INT NOT NULL',
            'sort' => 'INT NOT NULL DEFAULT 0',
            'quantity'=>'INT NOT NULL DEFAULT 0',
            'price'=>'DECIMAL(10,2) NOT NULL DEFAULT 0',
            'notify'=>'TINYINT NOT NULL DEFAULT 0',
        ));

        db()->createCommand()->update('{{product}}',[
            'remains'=>1,
        ]);

        if(!db()->createCommand()->select('key')->from('{{system_config}}')->where('`key`="site_currency"')->queryScalar())
            db()->createCommand()->insert('{{system_config}}', [
                'key'=>'site_currency',
                'value'=>'$'
            ]);

	}

	public function down()
	{
		
	}

}