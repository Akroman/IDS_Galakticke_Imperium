<?php
// source: D:\PHP\IDS\app\Presenters/templates/Jedi/default.latte

use Latte\Runtime as LR;

class Templatebb7458ecef extends Latte\Runtime\Template
{
	public $blocks = [
		'content' => 'blockContent',
		'_Jedi' => 'blockJedi',
	];

	public $blockTypes = [
		'content' => 'html',
		'_Jedi' => 'html',
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
			if (isset($this->params['request'])) trigger_error('Variable $request overwritten in foreach on line 13');
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
			?><a href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("Jedi:register")) ?>">Zaregistrovat nového Jedi</a><?php
		}
?>

</div>
    <table class="info">
    <tr>
        <th>ID</th>
        <th>Celé jméno</th>
        <th>Datum narození</th>
        <th>Rasa</th>
    </tr>
<div id="<?php echo htmlSpecialChars($this->global->snippetDriver->getHtmlId('Jedi')) ?>"><?php $this->renderBlock('_Jedi', $this->params) ?></div></table><?php
	}


	function blockJedi($_args)
	{
		extract($_args);
		$this->global->snippetDriver->enter("Jedi", "static");
		$iterations = 0;
		foreach ($requests as $request) {
?>
            <tr>
                <td><?php echo LR\Filters::escapeHtmlText($request->JEDI_ID) /* line 15 */ ?></td>
                <td><?php echo LR\Filters::escapeHtmlText($request->JMENO) /* line 16 */ ?> <?php echo LR\Filters::escapeHtmlText($request->PRIJMENI) /* line 16 */ ?></td>
                <td><?php echo LR\Filters::escapeHtmlText($request->NAROZENI) /* line 17 */ ?></td>
                <td><?php echo LR\Filters::escapeHtmlText($request->RASA) /* line 18 */ ?></td>
                <td><a style="color:#FFF" href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("show", [$request->JEDI_ID])) ?>">Zobrazit profil</a></td>
<?php
			if ($user->isInRole('Palpatine')) {
				?>                    <td><a style="color:#FFF" href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("edit", [$request->JEDI_ID])) ?>">Editovat profil</a></td>
                    <td><a style="color:#FFF" onclick="return confirm('Opravdu si přejete smazat profil?');" href="<?php
				echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("delete", [$request->JEDI_ID])) ?>">Smazat profil</a></td>
<?php
			}
?>
            </tr>
<?php
			$iterations++;
		}
		$this->global->snippetDriver->leave();
		
	}

}
