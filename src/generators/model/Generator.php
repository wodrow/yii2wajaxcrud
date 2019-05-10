<?php
/**
 * Created by PhpStorm.
 * User: wodrow
 * Date: 19-5-9
 * Time: 下午4:28
 */

namespace wodrow\wajaxcrud\generators\model;


use yii\gii\CodeFile;

class Generator extends \yii\gii\generators\model\Generator
{
    public $showName = 'WODROW MODEL GENERATOR';

    public $baseNs = 'common\models\db\tables';
    public $baseModelClass;
    public $extendModelClass;
    public $ns = 'common\models\db';
    public $queryNs = 'common\models\db\tables';
    public $useTablePrefix = true;
    public $generateLabelsFromComments = true;
    public $enableI18N = true;

    public function getName()
    {
        return $this->showName;
    }

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['baseNs', 'baseModelClass'], 'filter', 'filter' => 'trim'],
            [['baseNs'], 'filter', 'filter' => function ($value) { return trim($value, '\\'); }],

            [['baseNs'], 'required'],
            [['baseModelClass'], 'match', 'pattern' => '/^\w+$/', 'message' => 'Only word characters are allowed.'],
            [['baseNs'], 'match', 'pattern' => '/^[\w\\\\]+$/', 'message' => 'Only word characters and backslashes are allowed.'],
            [['baseNs'], 'validateNamespace'],
            [['baseModelClass'], 'validateBaseModelClass', 'skipOnEmpty' => false],
        ]);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'baseNs' => 'base Namespace',
            'baseModelClass' => 'Base Model Class Name',
        ]);
    }

    public function validateBaseModelClass()
    {
        if ($this->isReservedKeyword($this->baseModelClass)) {
            $this->addError('baseModelClass', 'Class name cannot be a reserved PHP keyword.');
        }
        if ((empty($this->tableName) || substr_compare($this->tableName, '*', -1, 1)) && $this->baseModelClass == '') {
            $this->addError('baseModelClass', 'Base Model Class cannot be blank if table name does not end with asterisk.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function requiredTemplates()
    {
        return ['baseModel.php', 'model.php'];
    }

    public function generate()
    {
        $files = [];
        $relations = $this->generateRelations();
        $db = $this->getDbConnection();
        foreach ($this->getTableNames() as $tableName) {
            // BaseModel :
            $modelClassName = $this->generateClassName($tableName);
            $queryClassName = ($this->generateQuery) ? $this->generateQueryClassName($modelClassName) : false;
            $tableSchema = $db->getTableSchema($tableName);
            $params = [
                'tableName' => $tableName,
                'className' => $modelClassName,
                'queryClassName' => $queryClassName,
                'tableSchema' => $tableSchema,
                'properties' => $this->generateProperties($tableSchema),
                'labels' => $this->generateLabels($tableSchema),
                'rules' => $this->generateRules($tableSchema),
                'relations' => isset($relations[$tableName]) ? $relations[$tableName] : [],
            ];
            $files[] = new CodeFile(
                \Yii::getAlias('@' . str_replace('\\', '/', $this->baseNs)) . '/' . $modelClassName . '.php',
                $this->render('baseModel.php', $params)
            );
            // Model :
            $modelClassName = $this->generateClassName($tableName);
            $queryClassName = ($this->generateQuery) ? $this->generateQueryClassName($modelClassName) : false;
            $tableSchema = $db->getTableSchema($tableName);
            $params = [
                'tableName' => $tableName,
                'className' => $modelClassName,
                'queryClassName' => $queryClassName,
                'tableSchema' => $tableSchema,
                'properties' => $this->generateProperties($tableSchema),
                'labels' => $this->generateLabels($tableSchema),
                'rules' => $this->generateRules($tableSchema),
                'relations' => isset($relations[$tableName]) ? $relations[$tableName] : [],
            ];
            $this->extendModelClass = $this->baseNs.'\\'.$this->baseModelClass;
            $files[] = new CodeFile(
                \Yii::getAlias('@' . str_replace('\\', '/', $this->ns)) . '/' . $modelClassName . '.php',
                $this->render('model.php', $params)
            );
            // query :
            if ($queryClassName) {
                $params['className'] = $queryClassName;
                $params['modelClassName'] = $modelClassName;
                $files[] = new CodeFile(
                    \Yii::getAlias('@' . str_replace('\\', '/', $this->queryNs)) . '/' . $queryClassName . '.php',
                    $this->render('query.php', $params)
                );
            }
        }

        return $files;
    }
}