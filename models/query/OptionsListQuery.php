<?php

namespace porcelanosa\yii2options\models\query;

/**
 * This is the ActiveQuery class for [[\app\modules\admin\models\OptionsList]].
 *
 * @see \app\modules\admin\models\OptionsList
 */
class OptionsListQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \app\modules\admin\models\OptionsList[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\modules\admin\models\OptionsList|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
