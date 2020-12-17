<?php

use yii\db\Migration;

/**
 * Class m201217_181648_statistic_report
 */
class m201217_181648_statistic_report extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%statistic_report}}', [
            'id' => $this->primaryKey(),
            'banner_id' => $this->integer(),
            'click' => $this->integer()->defaultValue(0),
            'shown' => $this->integer()->defaultValue(0),
            'ip' => $this->string(255)->null(),
            'geolocation' => $this->string(255)->null(),
            'ref_url' => $this->text()->null(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addForeignKey(
            'repeort_fk_banner',
            'statistic_report',
            'banner_id',
            'banners',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201217_181648_statistic_report cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201217_181648_statistic_report cannot be reverted.\n";

        return false;
    }
    */
}
