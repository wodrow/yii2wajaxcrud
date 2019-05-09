<?php
/**
 * Created by PhpStorm.
 * User: wodrow
 * Date: 19-5-9
 * Time: 下午4:28
 */

namespace wodrow\wajaxcrud\generators\model;


class Generator extends \yii\gii\generators\model\Generator
{
    public $showName = 'WODROW MODEL GENERATOR';

    public $ns = 'common\models\db\tables';
    public $queryNs = 'common\models\db\tables';
    public $useTablePrefix = true;
    public $generateLabelsFromComments = true;
    public $enableI18N = true;

    public function getName()
    {
        return $this->showName;
    }
}