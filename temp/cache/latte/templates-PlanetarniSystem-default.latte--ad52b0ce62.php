<?php
// source: D:\PHP\IDS\app\Presenters/templates/PlanetarniSystem/default.latte

use Latte\Runtime as LR;

class Templatead52b0ce62 extends Latte\Runtime\Template
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
			if (isset($this->params['planetarni_system'])) trigger_error('Variable $planetarni_system overwritten in foreach on line 21');
		}
		Nette\Bridges\ApplicationLatte\UIRuntime::initialize($this, $this->parentName, $this->blocks);
		
	}


	function blockContent($_args)
	{
		extract($_args);
?>
<div class="botnav">
<?php
		if ($user->isInRole('Palpatine')) {
			?>        <a href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("PlanetarniSystem:register")) ?>">Evidovat nový planetární systém</a>
        <a href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("Planety:register")) ?>">Zadat novou planetu</a>
        <a href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("Hvezdy:register")) ?>">Zadat novou hvězdu</a>
<?php
		}
?>
</div>
<?php
		/* line 9 */
		echo Nette\Bridges\FormsLatte\Runtime::renderFormBegin($form = $_form = $this->global->formsStack[] = $this->global->uiControl["searchForm"], ['style' => "margin-top: 10px"]);
?>

    <?php if ($_label = end($this->global->formsStack)["system"]->getLabel()) echo $_label->addAttributes(['style' => "color:#FFF"]) ?>

    <?php echo end($this->global->formsStack)["system"]->getControl() /* line 11 */ ?>

    <?php echo end($this->global->formsStack)["odeslat"]->getControl() /* line 12 */ ?>

<?php
		echo Nette\Bridges\FormsLatte\Runtime::renderFormEnd(array_pop($this->global->formsStack));
?>

<table class="info">
<tr>
	<th>ID</th>
	<th>Název</th>
    <th>Počet planet</th>
    <th>Počet hvězd</th>
</tr>
<?php
		$iterations = 0;
		foreach ($planetarni_systemy as $planetarni_system) {
?>
    <tr>
        <td><?php echo LR\Filters::escapeHtmlText($planetarni_system->ID) /* line 23 */ ?></td>
        <td><?php echo LR\Filters::escapeHtmlText($planetarni_system->NAZEV) /* line 24 */ ?></td>
        <td><?php echo LR\Filters::escapeHtmlText($planetarni_system->POCET_PLANET) /* line 25 */ ?></td>
        <td><?php echo LR\Filters::escapeHtmlText($planetarni_system->POCET_HVEZD) /* line 26 */ ?></td>
        <td><a style="color:#FFF" href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("Planety:", [$planetarni_system->ID])) ?>">Zobrazit planety</a></td>
        <td><a style="color:#FFF" href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("Hvezdy:", [$planetarni_system->ID])) ?>">Zobrazit hvězdy</a></td>
<?php
			if ($user->isInRole('Palpatine')) {
				?>            <td><a style="color:#FFF" href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("PlanetarniSystem:edit", [$planetarni_system->ID])) ?>">Editovat systém</a></td>
            <td><a style="color:#FFF" onclick="return confirm('Opravdu si přejete smazat planetarní systém?');" href="<?php
				echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("PlanetarniSystem:delete", [$planetarni_system->ID])) ?>">Smazat systém</a></td>
<?php
			}
?>
    </tr>
<?php
			$iterations++;
		}
		
	}

}
