<?php
// source: D:\PHP\IDS\app\Presenters/templates/Lode/default.latte

use Latte\Runtime as LR;

class Template744479edb3 extends Latte\Runtime\Template
{
	public $blocks = [
		'content' => 'blockContent',
	];

	public $blockTypes = [
		'content' => 'html',
	];


	function main()
	{
		extract($this->params);
		if ($this->getParentName()) return get_defined_vars();
		$this->renderBlock('content', get_defined_vars());
		return get_defined_vars();
	}


	function prepare()
	{
		extract($this->params);
		if (!$this->getReferringTemplate() || $this->getReferenceType() === "extends") {
			if (isset($this->params['lod'])) trigger_error('Variable $lod overwritten in foreach on line 24');
		}
		Nette\Bridges\ApplicationLatte\UIRuntime::initialize($this, $this->parentName, $this->blocks);
		
	}


	function blockContent($_args)
	{
		extract($_args);
		?><h1><?php echo LR\Filters::escapeHtmlText($velitel->NAZEV) /* line 2 */ ?></h1>
<div class="botnav">
<?php
		if ($user->isInRole('Palpatine') || $user->getId() == $velitel->VELITEL) {
			?>        <a href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("Lode:register", [$velitel->VELITEL, $flotila_id])) ?>">Přidat novou loď</a>
<?php
		}
?>
</div>
<?php
		/* line 8 */
		echo Nette\Bridges\FormsLatte\Runtime::renderFormBegin($form = $_form = $this->global->formsStack[] = $this->global->uiControl["searchForm"], ['style' => "margin-top: 10px"]);
?>

    <?php if ($_label = end($this->global->formsStack)["lod"]->getLabel()) echo $_label->addAttributes(['style' => "color:#FFF"]) ?>

    <?php echo end($this->global->formsStack)["lod"]->getControl() /* line 10 */ ?>

    <?php echo end($this->global->formsStack)["odeslat"]->getControl() /* line 11 */ ?>

<?php
		echo Nette\Bridges\FormsLatte\Runtime::renderFormEnd(array_pop($this->global->formsStack));
?>

<table class="info">
    <tr>
        <th>ID</th>
        <th>Typ</th>
        <th>Planeta výroby</th>
        <th>Stav štítů</th>
        <th>Stav motorů</th>
        <th>Míra poškození</th>
        <th>Počet turetů</th>
        <th>Kapacita</th>
    </tr>
<?php
		$iterations = 0;
		foreach ($lode as $lod) {
?>
        <tr>
            <td><?php echo LR\Filters::escapeHtmlText($lod->LOD_ID) /* line 26 */ ?></td>
            <td><?php
			if ($lod->TYP) {
				echo LR\Filters::escapeHtmlText($lod->TYP) /* line 27 */;
			}
			else {
				?>-<?php
			}
?></td>
            <td><?php echo LR\Filters::escapeHtmlText($lod->NAZEV) /* line 28 */ ?></td>
            <td><?php
			if ($lod->STITY) {
				echo LR\Filters::escapeHtmlText($lod->STITY) /* line 29 */;
			}
			else {
				?>-<?php
			}
?></td>
            <td><?php
			if ($lod->STAV_MOTORU) {
				echo LR\Filters::escapeHtmlText($lod->STAV_MOTORU) /* line 30 */;
			}
			else {
				?>-<?php
			}
?></td>
            <td><?php
			if ($lod->POSKOZENI) {
				echo LR\Filters::escapeHtmlText($lod->POSKOZENI) /* line 31 */;
			}
			else {
				?>-<?php
			}
?></td>
            <td><?php
			if ($lod->TURETY) {
				echo LR\Filters::escapeHtmlText($lod->TURETY) /* line 32 */;
			}
			else {
				?>-<?php
			}
?></td>
            <td><?php
			if ($lod->KAPACITA) {
				echo LR\Filters::escapeHtmlText($lod->KAPACITA) /* line 33 */;
			}
			else {
				?>-<?php
			}
?></td>
<?php
			if ($user->isInRole('Palpatine') || $user->getId() == $velitel->VELITEL) {
				?>                <td><a style="color:#FFF" href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("edit", [$lod->LOD_ID, $velitel->VELITEL, $flotila_id])) ?>">Editovat loď</a></td>
                <td><a style="color:#FFF" onclick="return confirm('Opravdu si přejete smazat loď?');" href="<?php
				echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("delete", [$lod->LOD_ID, $velitel->VELITEL, $flotila_id])) ?>">Vymazat loď</a></td>
<?php
			}
?>
        </tr>
<?php
			$iterations++;
		}
		
	}

}
