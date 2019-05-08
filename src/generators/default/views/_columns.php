<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;


/* @var $this yii\web\View */
/* @var $generator \wodrow\wajaxcrud\generators\Generator */

$modelClass = StringHelper::basename($generator->modelClass);
$urlParams = $generator->generateUrlParams();
$nameAttribute = $generator->getNameAttribute();
$actionParams = $generator->generateActionParams();

$editableFields = $generator->generateEditableFields();

echo "<?php\n";

?>
use yii\helpers\Url;
use kartik\grid\DataColumn;
use kartik\grid\SerialColumn;
use kartik\grid\EditableColumn;
use kartik\grid\CheckboxColumn;
use kartik\grid\ExpandRowColumn;
use kartik\grid\EnumColumn;
use kartik\grid\ActionColumn;

return [
    [
        'class' => CheckboxColumn::class,
        'width' => '20px',
    ],
    [
        'class' => SerialColumn::class,
        'width' => '30px',
    ],
    <?php foreach ($generator->getColumnNames() as $name): ?><?php if(in_array($name, $editableFields)): ?>[
        'class' => EditableColumn::class,
        'attribute' => '<?=$name ?>',
        'readonly' => function ($model, $key, $index, $widget) {
            return false;
        },
        'editableOptions' => function ($model, $key, $index, $widget) {
            return [
                'header' => '修改',
                'size' => 'md',
                'formOptions' => ['action' => ['editable-edit']],
            ];
        },
        'refreshGrid' => true,
    ],
    <?php else: ?>[
        'class' => DataColumn::class,
        'attribute' => '<?=$name ?>',
    ],
    <?php endif; ?>
    <?php endforeach; ?>
    [
        'class' => ActionColumn::class,
        'dropdown' => false,
        'vAlign'=>'middle',
        'urlCreator' => function($action, $model, $key, $index) { 
            return Url::to([$action,'<?=substr($actionParams,1)?>'=>$key]);
        },
        'viewOptions'=>['role'=>'modal-remote','title'=>'View','data-toggle'=>'tooltip'],
        'updateOptions'=>['role'=>'modal-remote','title'=>'Update', 'data-toggle'=>'tooltip'],
        'deleteOptions'=>[
            'role'=>'modal-remote','title'=>'Delete',
            'data-confirm'=>false, 'data-method'=>false,// for overide yii data api
            'data-request-method'=>'post',
            'data-toggle'=>'tooltip',
            'data-confirm-title'=>'删除数据提示!',
            'data-confirm-message'=>'你确认要删除本条数据吗?'
        ],
    ],

];   