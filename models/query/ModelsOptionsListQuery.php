<?php

namespace porcelanosa\yii2options\models\query;

/**
 * This is the ActiveQuery class for [[\app\modules\admin\models\ModelsOptionsList]].
 *
 * @see \app\modules\admin\models\ModelsOptionsList
 */
class ModelsOptionsListQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \porcelanosa\yii2options\models\ModelsOptionsList[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\modules\admin\models\ModelsOptionsList|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
