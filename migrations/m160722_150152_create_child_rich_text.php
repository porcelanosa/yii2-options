<?php

use yii\db\Migration;

class m160722_150152_create_child_rich_text extends Migration
{
    
    /*public function up()
    {

    }

    public function down()
    {
        echo "m160722_150152_create_child_rich_text cannot be reverted.\n";

        return false;
    }
*/
    
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
    
        $this->createTable('{{%porcelanosa_options_child_rich_texts}}', [
            'id' => $this->primaryKey(),
            'option_id' => $this->integer(),
            'title' => $this->string(255)->null(),
            'text' => $this->text()
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%porcelanosa_options_child_rich_texts}}');
    }
}
