<?
	use yii\helpers\Html;
	use app\components\helpers\MyHelper;
	
	/**
	 * @var $optionsList       \app\modules\admin\models\OptionsList[]
	 * @var $model_name        string
	 * @var $CommonOptionsList \app\modules\admin\models\OptionsList[]
	 */
?>
	<style>
		.sortable-ghost {
			color: green;
			border: 1px dotted rgba(100, 100, 100, .8);
		}
		
		#selected-options {
			min-height: 100px;
			height: auto;
			border: 1px solid silver;
		}

		#selected-options li i {
			color: #FF0000;
			cursor: pointer;
			font-weight: 600;
		}
		
		#cat-list li {
			cursor: pointer;
		}
		.selected-cat {
			font-family: 'Roboto Condensed', sans-serif;
			font-weight: 700;
		}
	</style>
<?
	$catTree = MyHelper::getTree(MyHelper::ADMIN_MODEL_NAMESPACE . 'Cats', 'parent_id')
?>
	<div class="row" id="CatsOptionsMain" data-modelName="<?=$model_name?>">

		<div class="col-md-2">
			<div class="row">
				<div class="col-md-12">
					<ul id="cat-list">
						<? foreach($catTree as $id => $cTr): ?>
							<li data-id="<?=$id?>" v-on:click="selectCat">
								<?=$cTr?>
							</li>
						<? endforeach; ?>
					</ul>
				</div>
			</div>
		</div>
		<div class="col-md-8">
			<div class="row">
				<div class="col-md-6">
					<!--<h4>Общие параметры</h4>
					<?/* foreach($CommonOptionsList as $option): */?>
						<?/*=$option->name*/?> (<?/*=$option->type->name*/?>)<br>
					<?/* endforeach; */?>
					<hr>-->
					<li v-for="item in ParentOptions">
						{{ optionName(item.option_id) }}
						{{ item.option_id }}
					</li>
					<ul id="selected-options">
						<li v-for="item in CurrentOptions" data-id="{{item.option_id}}">
							{{ optionName(item.option_id) }} {{item.option_id}} &nbsp;<i v-on:click="removeCurrOption(item)">✖</i>
						</li>
					</ul>
				</div>
			</div>
		</div>
		<div class="col-md-2">
			<div class="row">
				<div class="col-md-12">
					<ul id="options-list">
						<li v-for="item in Options" data-id="{{item.id}}">
							{{ item.name }}
						</li>
					</ul>
					<? /*=Html::dropDownList(
						'optionsList',
						[],
						\yii\helpers\ArrayHelper::map($optionsList, 'id', 'name'),
						[
							'id'       => 'options-list',
							'class'    => 'form-control',
							'multiple' => 'true'
						]

					)*/ ?>
				</div>
			</div>

		</div>
	</div>


<?
	/* $this->registerJsFile('/js/admin/Sortable.js', [
	'depends'  => [
		'yii\web\JqueryAsset',
	],
	'position' => \yii\web\View::POS_END
], 'Sortable-js');*/
?>

<? $this->registerJsFile('/js/admin/sortOptions.js', [
	'depends'  => [
		'\app\assets\SortablejsAsset',
		'\app\assets\VuejsAsset',
		'\app\assets\VueResourceAsset',
		'yii\web\JqueryAsset',
	],
	'position' => \yii\web\View::POS_END
], 'sort-options-js');
?>
<? $this->registerJsFile('/js/admin/CatsOptions.js', [
	'depends'  => [
		'\app\assets\SortablejsAsset',
		'\app\assets\VuejsAsset',
		'\app\assets\VueResourceAsset',
		'yii\web\JqueryAsset',
	],
	'position' => \yii\web\View::POS_END
], 'options-cats-list-js');
?>