<?php

use yii\db\Migration;

/**
 * Handles the creation of table `tbl_file_upload`.
 */
class m171120_201016_create_tbl_file_upload_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('tbl_file_upload', [
            'id' => $this->bigInteger()->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'section' => $this->string(150)->notNull(),
            'category' => $this->string(150)->notNull(),
            'refer_table' => $this->string(150)->notNull(),
            'refer_id' => $this->integer()->notNull(),
            'file_name' => $this->string(150)->notNull(),
            'file_name_original' => $this->string(150)->notNull(),
            'description' => $this->string(500),
            'mime_type' => $this->string(100)->notNull(),
            'file_size' => $this->bigInteger()->notNull(),
            'approved' => 'ENUM("yes", "no", "pending") NOT NULL',
            'relative_path' => $this->string(1000)->notNull(),
            'create_time' => $this->timestamp()->notNull(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('tbl_file_upload');
    }
}
