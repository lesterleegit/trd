<?php

namespace lesterleegit\trd\components;

use Yii;
use yii\di\Instance;
use yii\db\Connection;
use yii\helpers\ArrayHelper;

/**
 * This is just an example.
 */
class Configs extends \yii\base\Object
{
    public $db = 'db';
    public $userTable = '{{%user}}';
    private static $_instance;
    public $options;

    public static function instance()
    {
        if (self::$_instance === null) {
            $type = ArrayHelper::getValue(Yii::$app->params, 'lesterleegit.trd.configs', []);
            if (is_array($type) && !isset($type['class'])) {
                $type['class'] = static::className();
            }
            return self::$_instance = Yii::createObject($type);
        }
        return self::$_instance;
    }

    public static function __callStatic($name, $arguments)
    {
        $instance = static::instance();
        if ($instance->hasProperty($name)) {
            return $instance->$name;
        } else {
            if (count($arguments)) {
                $instance->options[$name] = reset($arguments);
            } else {
                return array_key_exists($name, $instance->options) ? $instance->options[$name] : null;
            }
        }
    }

    /**
     * @return Connection
     */
    public static function db()
    {
        return static::instance()->db;
    }

    /**
     * @return string
     */
    public static function userTable()
    {
        return static::instance()->userTable;
    }

//    private static $_classes = [
//        'db' => 'yii\db\Connection',
//    ];
//
//    public function init()
//    {
//        foreach (self::$_classes as $key => $class) {
//            try {
//                $this->{$key} = empty($this->{$key}) ? null : Instance::ensure($this->{$key}, $class);
//            } catch (\Exception $exc) {
//                $this->{$key} = null;
//                Yii::error($exc->getMessage());
//            }
//        }
//    }

}
