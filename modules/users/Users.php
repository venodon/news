<?php

namespace modules\users;

use yii\base\Module;

/**
 * users module definition class
 */
class Users extends Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'modules\users\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
}
