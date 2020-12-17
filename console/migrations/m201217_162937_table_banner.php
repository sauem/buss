<?php

use yii\db\Migration;

/**
 * Class m201217_162937_table_banner
 */
class m201217_162937_table_banner extends Migration
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
        $this->createTable('{{%banners}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255)->null(),
            'height' => $this->double()->defaultValue(0),
            'width' => $this->double()->defaultValue(0),
            'position' => $this->string(100),
            'sort' => $this->integer()->defaultValue(50),
            'href' => $this->string(255)->null(),
            'active' => $this->string(50),
            'page' => $this->string(255),
            'type' => $this->tinyInteger()->defaultValue(1),
            'is_random' => $this->tinyInteger()->defaultValue(0),
            'bellow_post' => $this->integer()->null(),
            'device' => $this->string(25)->defaultValue(0),
            'domain' => $this->string(255)->null(),
            'youtube_url' => $this->text()->null(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201217_162937_table_banner cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201217_162937_table_banner cannot be reverted.\n";

        return false;
    }
    */
}
