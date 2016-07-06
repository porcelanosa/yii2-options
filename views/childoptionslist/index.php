<?
	use yii\helpers\Html;
	use porcelanosa\yii2options\components\helpers\MyHelper;
	
	/**
	 * @var $optionsList \porcelanosa\yii2options\models\OptionsList[]
	 */
?>
<?
	$catTree = MyHelper::getTree(MyHelper::ADMIN_MODEL_NAMESPACE . 'Cats', 'parent_id')
?>
	<div class="row" style="height: ">
		<div class="col-md-2">
			<div class="row">
				<div class="col-md-12">
					<ul id="cat-list">
						<? foreach ($catTree as $id=>$cTr): ?>
							<li data-id="<?=$id?>"><?=$cTr?></li>
						<? endforeach; ?>
					</ul>
					<? /*=Html::dropDownList(
					'catsList',
					[],
					$catTree,
					[
						'id'       => 'cats-list',
						'class'    => 'form-control',
						'multiple' => 'true'
					]
				)*/ ?>

				</div>
			</div>
		</div>
		<style>
			.sortable-ghost {
				color: green;
				border: 1px dotted rgba(100, 100, 100, .8);
			}
			#selected-cats, #selected-options {
				min-height: 100px;
				height: auto;
				border: 1px solid silver;
			}
			.js-remove {
				cursor: pointer;
				color: red;
			}
		</style>
		<div class="col-md-8" id='optionsCatsList'>
			<div class="row">
				<div class="col-md-6">
					<ul id="selected-cats"></ul>
				</div>
				<div class="col-md-6">
					<ul id="selected-options"></ul>
				</div>
			</div>
		</div>
		<div class="col-md-2">
			<div class="row">
				<div class="col-md-12">
					<ul id="options-list">
						<? foreach ($optionsList as $option): ?>
							<li data-id="<?=$option->id?>"><?=$option->name?></li>
						<? endforeach; ?>
					</ul>
					<?/*=Html::dropDownList(
						'optionsList',
						[],
						\yii\helpers\ArrayHelper::map($optionsList, 'id', 'name'),
						[
							'id'       => 'options-list',
							'class'    => 'form-control',
							'multiple' => 'true'
						]

					)*/?>
				</div>
			</div>

		</div>
		<pre><?
				print_r($return_array);
			?></pre>
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
<? $this->registerJsFile('/js/admin/optionsCatsList.js', [
	'depends'  => [
		'\app\assets\SortablejsAsset',
		'\app\assets\VuejsAsset',
		'\app\assets\VueResourceAsset',
		'yii\web\JqueryAsset',
	],
	'position' => \yii\web\View::POS_END
], 'options-cats-list-js');
?>