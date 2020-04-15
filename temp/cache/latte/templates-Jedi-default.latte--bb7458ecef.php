<?php
// source: D:\PHP\IDS\app\Presenters/templates/Jedi/default.latte

use Latte\Runtime as LR;

class Templatebb7458ecef extends Latte\Runtime\Template
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
			if (isset($this->params['request'])) trigger_error('Variable $request overwritten in foreach on line 17');
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
<?php
		/* line 5 */
		echo Nette\Bridges\FormsLatte\Runtime::renderFormBegin($form = $_form = $this->global->formsStack[] = $this->global->uiControl["searchForm"], []);
?>

    <?php if ($_label = end($this->global->formsStack)["jedi"]->getLabel()) echo $_label->addAttributes(['style' => "color:#FFF"]) ?>

    <?php echo end($this->global->formsStack)["jedi"]->getControl() /* line 7 */ ?>

    <?php echo end($this->global->formsStack)["odeslat"]->getControl() /* line 8 */ ?>

<?php
		echo Nette\Bridges\FormsLatte\Runtime::renderFormEnd(array_pop($this->global->formsStack));
?>

    <table class="info">
    <tr>
        <th>ID</th>
        <th>Celé jméno</th>
        <th>Datum narození</th>
        <th>Rasa</th>
    </tr>
<?php
		$iterations = 0;
		foreach ($requests as $request) {
?>
        <tr>
            <td><?php echo LR\Filters::escapeHtmlText($request->JEDI_ID) /* line 19 */ ?></td>
            <td><?php echo LR\Filters::escapeHtmlText($request->JMENO) /* line 20 */ ?> <?php echo LR\Filters::escapeHtmlText($request->PRIJMENI) /* line 20 */ ?></td>
            <td><?php echo LR\Filters::escapeHtmlText($request->NAROZENI) /* line 21 */ ?></td>
            <td><?php echo LR\Filters::escapeHtmlText($request->RASA) /* line 22 */ ?></td>
            <td><a style="color:#FFF" href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("show", [$request->JEDI_ID])) ?>">Zobrazit profil</a></td>
<?php
			if ($user->isInRole('Palpatine')) {
				?>                <td><a style="color:#FFF" href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("edit", [$request->JEDI_ID])) ?>">Editovat profil</a></td>
                <td><a style="color:#FFF" onclick="return confirm('Opravdu si přejete smazat profil?');" href="<?php
				echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("delete", [$request->JEDI_ID])) ?>">Smazat profil</a></td>
<?php
			}
?>
        </tr>
<?php
			$iterations++;
		}
		?></table><?php
	}

}
