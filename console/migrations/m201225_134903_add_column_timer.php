<?php

use yii\db\Migration;

/**
 * Class m201225_134903_add_column_timer
 */
class m201225_134903_add_column_timer extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('banners', 'timer_start', $this->integer()->null());
        $this->addColumn('banners', 'timer_end', $this->integer()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201225_134903_add_column_timer cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201225_134903_add_column_timer cannot be reverted.\n";

        return false;
    }
    */
}
