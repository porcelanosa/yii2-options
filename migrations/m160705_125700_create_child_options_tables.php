<?php

use yii\db\Migration;

class m160705_125700_create_child_options_tables extends Migration
{
    public function up()
    {
	
	    $tableOptions = null;
	    if ($this->db->driverName === 'mysql') {
		    $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
	    }
	
	    $this->createTable('{{%models_options_list}}', [
		    'model_id' => $this->integer(),
		    'model_name' => $this->string(255)->notNull(),
		    'option_id' => $this->integer(),
		    'sort' => $this->integer()
	    ], $tableOptions);
	    $this->createTable('{{%child_options}}', [
		    'id' => $this->primaryKey(),
		    'model_id' => $this->integer(),
		    'model' => $this->string(255)->notNull(),
		    'option_id' => $this->integer(),
		    'value' => $this->string(255)->notNull(),
	    ], $tableOptions);
	    $this->createTable('{{%child_option_multiple}}', [
		    'id' => $this->primaryKey(),
		    'option_id' => $this->integer(),
		    'value' => $this->string(255)->notNull(),
	    ], $tableOptions);
    }

    public function down()
    {
	    $this->dropTable('{{%models_options_list}}');
	    $this->dropTable('{{%child_options}}');
	    $this->dropTable('{{%child_option_multiple}}');
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
