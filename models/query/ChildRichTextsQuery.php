<?php

namespace porcelanosa\yii2options\models\query;

/**
 * This is the ActiveQuery class for [[\vendor\porcelanosa\yii2options\models\ChildRichTexts]].
 *
 * @see \vendor\porcelanosa\yii2options\models\ChildRichTexts
 */
class ChildRichTextsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \porcelanosa\yii2options\models\ChildRichTexts[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \porcelanosa\yii2options\models\ChildRichTexts|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
