<?php

use yii\db\Migration;

class m160705_074925_insert_base_type extends Migration
{
    public function up()
    {
	    $this->insert('{{%option_types}}', [
		    'name' => 'Да/Нет',
		    'alias' => 'boolean',
		    'sort' => 1,
		    'active' => 1,
	    ]);
	    $this->insert('{{%option_types}}', [
		    'name' => 'Текстовое поле',
		    'alias' => 'textinput',
		    'sort' => 2,
		    'active' => 1,
	    ]);
	    $this->insert('{{%option_types}}', [
		    'name' => 'Большой текст',
		    'alias' => 'textarea',
		    'sort' => 3,
		    'active' => 1,
	    ]);
	    $this->insert('{{%option_types}}', [
		    'name' => 'Редактор',
		    'alias' => 'richtext',
		    'sort' => 4,
		    'active' => 1,
	    ]);
	    $this->insert('{{%option_types}}', [
		    'name' => 'Выпадающий список',
		    'alias' => 'dropdown',
		    'sort' => 5,
		    'active' => 1,
	    ]);
	    $this->insert('{{%option_types}}', [
		    'name' => 'Выпадающий список c множественным выбором',
		    'alias' => 'dropdown-multiple',
		    'sort' => 6,
		    'active' => 1,
	    ]);
	    $this->insert('{{%option_types}}', [
		    'name' => 'Список выбора (radio buton)',
		    'alias' => 'radiobuton_list',
		    'sort' => 7,
		    'active' => 1,
	    ]);
	    $this->insert('{{%option_types}}', [
		    'name' => 'Список выбора (checkbox list)',
		    'alias' => 'checkboxlist-multiple',
		    'sort' => 8,
		    'active' => 1,
	    ]);
	    $this->insert('{{%option_types}}', [
		    'name' => 'Изображение',
		    'alias' => 'image',
		    'sort' => 9,
		    'active' => 1,
	    ]);
    }

    public function down()
    {
        $this->delete('{{%option_types}}', 'alias=:alias', [':alias'=>'boolean']);
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
