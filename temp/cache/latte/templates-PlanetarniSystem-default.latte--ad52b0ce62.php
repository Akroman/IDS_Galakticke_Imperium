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
		Nette\Bridges\ApplicationLatte\UIRuntime::initialize($this, $this->parentName, $this->blocks);
		
	}


	function blockContent($_args)
	{
		extract($_args);
?>
<div class="botnav">
    <?php
		if ($user->isInRole('Palpatine')) {
			?><a href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("PlanetarniSystem:register")) ?>">Evidovat nový planetární systém</a><?php
		}
?>

</div>
<table class="info">
<tr>
	<th>ID</th>
	<th>Název</th>
    <th>Datum narození</th>
    <th>Rasa</th>
</tr><?php
	}

}
