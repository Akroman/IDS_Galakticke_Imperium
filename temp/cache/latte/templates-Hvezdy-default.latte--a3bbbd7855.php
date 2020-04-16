<?php
// source: D:\PHP\IDS\app\Presenters/templates/Hvezdy/default.latte

use Latte\Runtime as LR;

class Templatea3bbbd7855 extends Latte\Runtime\Template
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
			if (isset($this->params['hvezda'])) trigger_error('Variable $hvezda overwritten in foreach on line 12');
		}
		Nette\Bridges\ApplicationLatte\UIRuntime::initialize($this, $this->parentName, $this->blocks);
		
	}


	function blockContent($_args)
	{
		extract($_args);
		?><h1><?php echo LR\Filters::escapeHtmlText($system->NAZEV) /* line 2 */ ?></h1>
<div class="botnav">
    <?php
		if ($user->isInRole('Palpatine')) {
			?><a href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("Hvezdy:register")) ?>">Zadat novou hvězdu</a><?php
		}
?>

</div>
<table class="info">
<tr>
	<th>ID</th>
	<th>Název</th>
    <th>Typ</th>
</tr>
<?php
		$iterations = 0;
		foreach ($hvezdy as $hvezda) {
?>
    <tr>
        <td><?php echo LR\Filters::escapeHtmlText($hvezda->HVEZDA_ID) /* line 14 */ ?></td>
        <td><?php echo LR\Filters::escapeHtmlText($hvezda->NAZEV) /* line 15 */ ?></td>
        <td><?php echo LR\Filters::escapeHtmlText($hvezda->TYP) /* line 16 */ ?></td>
<?php
			if ($user->isInRole('Palpatine')) {
				?>            <td><a style="color:#FFF" href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("Hvezdy:edit", [$hvezda->HVEZDA_ID, $hvezda->SYSTEM_ID])) ?>">Editovat hvězdu</a></td>
            <td><a style="color:#FFF" onclick="return confirm('Opravdu si přejete smazat planetarní systém?');" href="<?php
				echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("Hvezdy:delete", [$hvezda->HVEZDA_ID, $hvezda->SYSTEM_ID])) ?>">Smazat hvězdu</a></td>
<?php
			}
?>
    </tr>
<?php
			$iterations++;
		}
		
	}

}
