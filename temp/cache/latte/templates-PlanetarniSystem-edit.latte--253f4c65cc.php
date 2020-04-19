<?php
// source: D:\PHP\IDS\app\Presenters/templates/PlanetarniSystem/edit.latte

use Latte\Runtime as LR;

class Template253f4c65cc extends Latte\Runtime\Template
{
	public $blocks = [
		'content' => 'blockContent',
		'title' => 'blockTitle',
	];

	public $blockTypes = [
		'content' => 'html',
		'title' => 'html',
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

<?php
		$this->renderBlock('title', get_defined_vars());
?>

<div class="botnav">
<?php
		if ($user->isInRole('Palpatine')) {
			?>        <a href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("PlanetarniSystem:register")) ?>">Evidovat nový planetární systém</a>
        <a href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("Planety:register", [$system])) ?>">Zadat novou planetu</a>
        <a href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("Hvezdy:register", [$system])) ?>">Zadat novou hvězdu</a>
<?php
		}
?>
</div>

<?php
		/* line 13 */ $_tmp = $this->global->uiControl->getComponent("registerForm");
		if ($_tmp instanceof Nette\Application\UI\IRenderable) $_tmp->redrawControl(null, false);
		$_tmp->render();
		
	}


	function blockTitle($_args)
	{
		extract($_args);
		?><h1>Editace planetárního systému <?php echo LR\Filters::escapeHtmlText($nazev) /* line 3 */ ?></h1>
<?php
	}

}
