<?php

namespace porcelanosa\yii2options\models\query;

/**
 * This is the ActiveQuery class for [[\app\modules\admin\models\Options]].
 *
 * @see \app\modules\admin\models\Options
 */
class OptionsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \app\modules\admin\models\Options[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\modules\admin\models\Options|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
