<?php
namespace App\Traits;

// https://github.com/ThingEngineer/PHP-MySQLi-Database-Class
use MysqliDb;

trait Databases
{
    private $instance;
    public static $DB;

    public function __construct()
    {
        echo "__construct";
    }

    public static function init()
    {
        self::$DB = new MysqliDb ([
            'host' => $_ENV["DB_HOST"],
            'username' => $_ENV["DB_USERNAME"],
            'password' => $_ENV["DB_PASSWORD"],
            'db'=> $_ENV["DB_DATABASE"],
            'port' => $_ENV["DB_PORT"]
        ]);
    }

    public static function getNicepageMediaFiles()
    {
        $db = self::$DB;

        $files = $db->get('media_files');
        print_r($files);
    }

    public static function insertNicapageMediaFiles($params = [])
    {
        $db = self::$DB;

        $task = $db->insert ('media_files', [
            'category' => isset($params['category']) && $params['category'] ? $params['category'] : NULL,
            'dest_path' => isset($params['dest_path']) && $params['dest_path'] ? $params['dest_path'] : NULL,
            'file_name' => isset($params['file_name']) && $params['file_name'] ? $params['file_name'] : NULL,
            'original_name' => isset($params['original_name']) && $params['original_name'] ? $params['original_name'] : NULL,
            'file_type' => isset($params['file_type']) && $params['file_type'] ? $params['file_type'] : NULL,
            'file_size' => isset($params['file_size']) && $params['file_size'] ? $params['file_size'] : NULL,
            'file_extension' => isset($params['file_extension']) && $params['file_extension'] ? $params['file_extension'] : NULL,
            'active' => 'Y',
            'created_at' => $db->now(),
            'updated_at' => $db->now()
        ]);

        if(!$task) {

            return [
                'state' => false,
                'error' => $db->getLastError()
            ];
        }

        return [
            'state' => true
        ];
    }
}