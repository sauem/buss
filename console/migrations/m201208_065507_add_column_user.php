<?php

use yii\db\Migration;

/**
 * Class m201208_065507_add_column_user
 */
class m201208_065507_add_column_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user','full_name', $this->string(255)->null());
        $this->addColumn('user','phone_of_day', $this->integer()->defaultValue(0));
        $this->addColumn('user','country', $this->string(255)->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201208_065507_add_column_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201208_065507_add_column_user cannot be reverted.\n";

        return false;
    }
    */
}
