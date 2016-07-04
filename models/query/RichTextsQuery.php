<?php

namespace porcelanosa\yii2options\models\query;

/**
 * This is the ActiveQuery class for [[\porcelanosa\yii2options\models\RichTexts]].
 *
 * @see porcelanosa\yii2options\models\RichTexts
 */
class RichTextsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \porcelanosa\yii2options\models\RichTexts []|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \porcelanosa\yii2options\models\RichTexts|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
