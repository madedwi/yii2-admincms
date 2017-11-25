<?php

use yii\db\Migration;

/**
 * Handles the creation of table `post`.
 */
class m171113_060032_create_post_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('user', [
            'id' => "int(11) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY",
            'username' => "varchar(255) COLLATE utf8_unicode_ci NOT NULL",
            'auth_key' => "varchar(32) COLLATE utf8_unicode_ci NOT NULL",
            'password_hash' => "varchar(255) COLLATE utf8_unicode_ci NOT NULL",
            'password_reset_token' => "varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL",
            'timezone' => "varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Asia/Makassar'",
            'email' => "varchar(255) COLLATE utf8_unicode_ci NOT NULL",
            'status' => "varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'notactive'",
            'created_at' => "int(11) NOT NULL",
            'updated_at' => "int(11) NOT NULL",
            'parent' => "int(11) unsigned DEFAULT NULL",
            'type' => "varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL",
            'firstname' => "varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL",
            'lastname' => "varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL",
            'phone' => "varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL",
        ]);
        $this->createIndex('u_user_username', 'user', 'username', true);
        $this->createIndex('u_user_email', 'user', 'email', true);
        $this->createIndex('u_user_password_reset_token', 'user', 'password_reset_token', true);

        $user = new \admin\models\User();
        $user->scenario = \admin\models\User::SCENARIO_REGISTER;
        $user->username = 'administrator';
        $user->email    = 'administrator@domain.com';
        $user->status   = 'active';
        $user->type     = 'root';
        $user->firstname = 'Administrator';
        $user->lastname  = 'Dummy';
        $user->phone     = '+6281999777666';

        $user->password = 'administrator';
        $user->password_repeat = 'administrator';

        if(!($user->validate() && $user->save())){
            throw new \Exception(json_encode($user->firstErrors));

        }

        $this->createTable('post', [
            'id'     => 'bigint(20) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY',
            'parent' => 'int(11) DEFAULT 0',
            'title'  =>  'varchar(255) CHARACTER SET utf8mb4 NOT NULL',
            'content'   => 'longtext CHARACTER SET utf8mb4',
            'slug'  => 'varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL',
            'type'  =>  "varchar(15) NOT NULL DEFAULT 'post'",
            'views' => "bigint(20) unsigned DEFAULT '0'",
            'status' => "varchar(15) NOT NULL DEFAULT 'draft'",
            'layout' => "varchar(50) DEFAULT 'singlepage'",
            'postdate'      => "datetime DEFAULT NULL",
            'publishdate'   => "datetime DEFAULT NULL",
            'postby'    => "int(11) DEFAULT '0'",
            'modified'  => "datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP",
            'postsort'  => "int(11) DEFAULT NULL",
        ]);
        $this->createIndex('FK_user_post', 'post', 'postby');
        $this->createIndex('IDX_title_type', 'post', ['type', 'status']);

        $this->createTable('post_meta', [
            'post_id' => "int(11) DEFAULT NULL",
            'metakey' => "varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL",
            'value' => "longtext CHARACTER SET utf8mb4"
        ]);
        $this->createIndex('u_post_meta', 'post_meta', ['post_id', 'metakey'], true);

        $this->createTable('terms', [
            'id' => "bigint(20) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY",
            'terms' => "varchar(100) CHARACTER SET utf8mb4 NOT NULL",
            'terms_slug' => "varchar(100) CHARACTER SET utf8mb4 NOT NULL",
            'terms_description' => "text CHARACTER SET utf8mb4",
            'parent' => "bigint(20) DEFAULT '0'",
            'type' => "varchar(50) DEFAULT 'category'",
        ]);
        $this->createIndex('u_terms', 'terms', ['terms', 'type'], true);
        $this->createIndex('IDX_terms_type', 'terms', 'type');

        $this->createTable('post_terms', [
            'post_id'  => "bigint(20) NOT NULL",
            'terms_id' => "bigint(20) NOT NULL",
            'modified' => "datetime NOT NULL DEFAULT '".date('Y-m-d H:i:s')."' ON UPDATE CURRENT_TIMESTAMP",
        ]);
        $this->createIndex('u_post_terms', 'post_terms', ['post_id', 'terms_id'], true);

        $this->createTable('uploads', [
            'id' => "int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY",
            'type' => "varchar(15) CHARACTER SET utf8mb4 NOT NULL",
            'filename' => "varchar(50) CHARACTER SET utf8mb4 NOT NULL",
            'fileurl' => "varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT ''",
            'alt' => "varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL",
            'title' => "varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL",
            'uploaddate' => "datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP",
            'description' => "varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL",
        ]);

        $this->createTable('web_options', [
            'id' => $this->bigInteger(20) . ' UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY',
            'option_key' => 'varchar(50) DEFAULT NULL',
            'option_value' => 'longtext'
        ]);
        $this->createIndex('u_opt_key', 'web_options', 'option_key', true);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('web_options');
        $this->dropTable('uploads');
        $this->dropTable('post_terms');
        $this->dropTable('terms');
        $this->dropTable('post_meta');
        $this->dropTable('post');
        $this->dropTable('user');
    }
}
