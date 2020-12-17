<?php

use yii\db\Migration;

/**
 * Class m201209_031029_media
 */
class m201209_031029_media extends Migration
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
        $this->createTable('{{%media}}', [
            'id' => $this->primaryKey(),
            'url' => $this->text(),
            'path' => $this->text()->null(),
            'type' => $this->integer()->defaultValue(0),
            'status' => $this->integer()->defaultValue(1),
            'alter' => $this->string(255)->null(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createTable('{{%media_obj}}', [
            'id' => $this->primaryKey(),
            'obj_id' => $this->integer(),
            'media_id' => $this->integer(),
            'obj_type' => $this->string(255),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addForeignKey('media_fk_obj', 'media_obj', 'media_id', 'media', 'id', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201209_031029_media cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201209_031029_media cannot be reverted.\n";

        return false;
    }
    */
}
