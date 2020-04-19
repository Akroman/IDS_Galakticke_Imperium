<?php
// source: D:\PHP\IDS\app\Presenters/templates/Planety/default.latte

use Latte\Runtime as LR;

class Template4507f2fecf extends Latte\Runtime\Template
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
			if (isset($this->params['planeta'])) trigger_error('Variable $planeta overwritten in foreach on line 14');
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
			?><a href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("Planety:register", [$system->SYSTEM_ID])) ?>">Zadat novou planetu</a><?php
		}
?>

</div>
<table class="info">
<tr>
	<th>ID</th>
	<th>Název</th>
    <th>Typ</th>
    <th>Vzdálenost od slunce</th>
    <th>Počet obyvatel</th>
</tr>
<?php
		$iterations = 0;
		foreach ($planety as $planeta) {
?>
    <tr>
        <td><?php echo LR\Filters::escapeHtmlText($planeta->PLANETA_ID) /* line 16 */ ?></td>
        <td><?php echo LR\Filters::escapeHtmlText($planeta->NAZEV) /* line 17 */ ?></td>
        <td><?php echo LR\Filters::escapeHtmlText($planeta->TYP) /* line 18 */ ?></td>
        <td><?php echo LR\Filters::escapeHtmlText($planeta->VZDALENOST_SLUNCE) /* line 19 */ ?></td>
        <td><?php echo LR\Filters::escapeHtmlText($planeta->POCET_OBYVATEL) /* line 20 */ ?></td>
<?php
			if ($user->isInRole('Palpatine')) {
				?>            <td><a style="color:#FFF" href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("Planety:edit", [$planeta->PLANETA_ID, $planeta->SYSTEM_ID])) ?>">Editovat planetu</a></td>
            <td><a style="color:#FFF" onclick="return confirm('Opravdu si přejete smazat planetarní systém?');" href="<?php
				echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("Planety:delete", [$planeta->PLANETA_ID, $planeta->SYSTEM_ID])) ?>">Smazat planetu</a></td>
<?php
			}
?>
    </tr>
<?php
			$iterations++;
		}
		
	}

}
