<?php
/**
 * Created by PhpStorm.
 * User: venodon
 * Date: 24.07.2018
 * Time: 10:12
 */
namespace modules\config\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "config".
 *
 * @property int $id
 * @property int $parent_id
 * @property string $slug
 * @property string $name
 * @property array $typesArray
 * @property int $type
 * @property string $value
 * @property string $variants
 */
class Config extends ActiveRecord
{
    public const TYPE_INPUT = 1;
    public const TYPE_INTEGER = 2;
    public const TYPE_NUMBER = 3;
    public const TYPE_CHECKBOX = 4;
    public const TYPE_SELECT = 5;
    public const TYPE_PURE_TEXTAREA = 9;

    /**
     * @translate
     */
    public const TYPES = [
        self::TYPE_INPUT         => 'Input field',
        self::TYPE_INTEGER       => 'Integer',
        self::TYPE_NUMBER        => 'Float',
        self::TYPE_CHECKBOX      => 'Checkbox',
        self::TYPE_SELECT        => 'Dropdown list',
        self::TYPE_PURE_TEXTAREA => 'Textarea',
    ];

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        $array = explode(',', $this->variants);
        foreach ($array as $k => $item) {
            $array[$k] = trim($item);
        }
        $this->variants = implode(', ', $array);
        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'config';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['parent_id', 'type'], 'integer'],
            [['value', 'variants'], 'string'],
            [['slug'], 'unique'],
            [['slug'], 'string', 'max' => 50],
            [['name'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'        => 'ID',
            'parent_id' => 'Category',
            'slug'      => 'Slug',
            'name'      => 'Name',
            'type'      => 'Type',
            'value'     => 'Value',
            'variants'  => 'Variants',
        ];
    }

    /**
     * @return array
     */
    public function getVariants()
    {
        $arr = explode(', ', $this->variants);
        return array_combine($arr, $arr);
    }

    /**
     * @param string $slug
     * @return Config|null
     */
    public static function getConfig(string $slug)
    {
        if ($config = Config::findOne(['slug' => $slug])) {
            return $config;
        }
        return null;
    }

    /**
     * @param string $slug
     * @return string|null
     */
    public static function getValue(string $slug)
    {
        return Yii::$app->cache->getOrSet('config_parameter_' . $slug, function () use ($slug) {
            $config = self::getConfig($slug);
            if ($config) {
                return $config->value;
            }
            return null;
        }, 3600);
    }
}
