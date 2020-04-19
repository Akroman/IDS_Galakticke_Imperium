<?php
// source: D:\PHP\IDS\app\Presenters/templates/Flotila/default.latte

use Latte\Runtime as LR;

class Templatec91a2fc4d2 extends Latte\Runtime\Template
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
			if (isset($this->params['flotila'])) trigger_error('Variable $flotila overwritten in foreach on line 20');
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
			?>        <a href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("Flotila:register")) ?>">Přidat novou flotilu</a>
        <a href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("Lode:register")) ?>">Vytvořit novou loď</a>
<?php
		}
?>
</div>
<?php
		/* line 8 */
		echo Nette\Bridges\FormsLatte\Runtime::renderFormBegin($form = $_form = $this->global->formsStack[] = $this->global->uiControl["searchForm"], ['style' => "margin-top: 10px"]);
?>

    <?php if ($_label = end($this->global->formsStack)["flotila"]->getLabel()) echo $_label->addAttributes(['style' => "color:#FFF"]) ?>

    <?php echo end($this->global->formsStack)["flotila"]->getControl() /* line 10 */ ?>

    <?php echo end($this->global->formsStack)["odeslat"]->getControl() /* line 11 */ ?>

<?php
		echo Nette\Bridges\FormsLatte\Runtime::renderFormEnd(array_pop($this->global->formsStack));
?>

<table class="info">
    <tr>
        <th>ID</th>
        <th>Název</th>
        <th>Velitel</th>
        <th>Počet členů flotily</th>
    </tr>
<?php
		$iterations = 0;
		foreach ($flotily as $flotila) {
?>
        <tr>
            <td><?php echo LR\Filters::escapeHtmlText($flotila->FLOTILA_ID) /* line 22 */ ?></td>
            <td><?php echo LR\Filters::escapeHtmlText($flotila->NAZEV) /* line 23 */ ?></td>
            <td><?php echo LR\Filters::escapeHtmlText($flotila->JMENO) /* line 24 */ ?> <?php echo LR\Filters::escapeHtmlText($flotila->PRIJMENI) /* line 24 */ ?></td>
            <td><?php echo LR\Filters::escapeHtmlText($flotila->POCET_CLENU) /* line 25 */ ?></td>
            <td><a style="color:#FFF" href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("show", [$flotila->FLOTILA_ID])) ?>">Detail flotily</a></td>
<?php
			if ($user->isInRole('Palpatine')) {
				?>                <td><a style="color:#FFF" href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("edit", [$flotila->FLOTILA_ID])) ?>">Editovat flotilu</a></td>
                <td><a style="color:#FFF" href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("delete", [$flotila->FLOTILA_ID])) ?>">Vymazat flotilu</a></td>
<?php
			}
			?>            <td><a style="color:#FFF" href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("Lode:", [$flotila->FLOTILA_ID])) ?>">Zobrazit lodě flotily</a></td>
        </tr>
<?php
			$iterations++;
		}
		
	}

}
