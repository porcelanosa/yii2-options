<?php
	use yii\db\Migration;

class m160704_165914_options_migration extends Migration
{
    public function up()
    {
	    $tableOptions = null;
	    if ($this->db->driverName === 'mysql') {
		    $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
	    }
	    $this->createTable('{{%options}}', [
		    'id' => $this->primaryKey(),
		    'model_id' => $this->integer(),
		    'model' => $this->string(255)->notNull(),
		    'option_id' => $this->integer(),
		    'value' => $this->string(255)->notNull(),
	    ], $tableOptions);
	    $this->createTable('{{%option_types}}', [
		    'id' => $this->primaryKey(),
		    'name' => $this->string(255)->notNull(),
		    'alias' => $this->string(255)->notNull(),
		    'sort' => $this->integer(),
		    'active' => $this->integer(),
	    ], $tableOptions);
	    $this->createTable('{{%option_multiple}}', [
		    'id' => $this->primaryKey(),
		    'option_id' => $this->integer(),
		    'value' => $this->string(255)->notNull(),
	    ], $tableOptions);
	    $this->createTable('{{%option_presets}}', [
		    'id' => $this->primaryKey(),
		    'defaultPresetValue_id' => $this->integer(),
		    'name' => $this->string(255)->notNull(),
		    'short_name' => $this->string(255)->notNull(),
		    'sort' => $this->integer(),
		    'active' => $this->integer(),
	    ], $tableOptions);
	    $this->createTable('{{%option_preset_values}}', [
		    'id' => $this->primaryKey(),
		    'preset_id' => $this->integer(),
		    'value' => $this->string(255)->notNull(),
		    'sort' => $this->integer(),
		    'active' => $this->integer(),
	    ], $tableOptions);
	    $this->createTable('{{%rich_texts}}', [
		    'id' => $this->primaryKey(),
		    'option_id' => $this->integer(),
		    'title' => $this->string(255)->notNull(),
		    'text' => $this->text()
	    ], $tableOptions);
	    $this->createTable('{{%options_list}}', [
		    'id' => $this->primaryKey(),
		    'name' => $this->string(255)->notNull(),
		    'alias' => $this->string(255)->notNull(),
		    'model' => $this->string(255)->notNull(),
		    'is_required' => $this->integer(),
		    'in_filter' => $this->integer(),
		    'type_id' => $this->integer(),
		    'preset_id' => $this->integer(),
		    'minLenght' => $this->integer(),
		    'maxLenght' => $this->integer(),
		    'active' => $this->integer(),
		    'sort' => $this->integer(),
	    ], $tableOptions);
    }

    public function down()
    {
	    $this->dropTable('{{%options}}');
	    $this->dropTable('{{%option_types}}');
	    $this->dropTable('{{%option_multiple}}');
	    $this->dropTable('{{%option_presets}}');
	    $this->dropTable('{{%option_preset_values}}');
	    $this->dropTable('{{%rich_texts}}');
	    $this->dropTable('{{%options_list}}');
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
