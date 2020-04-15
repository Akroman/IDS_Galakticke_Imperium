<?php
// source: D:\PHP\IDS\app\Presenters/templates/Flotila/show.latte

use Latte\Runtime as LR;

class Templatec2600159ac extends Latte\Runtime\Template
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
	<a href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("Lode:", [$flotila->FLOTILA_ID])) ?>">Zobrazit lodě flotily</a>
<?php
		if ($user->isInRole('Palpatine')) {
			?>        <a href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("Flotila:register")) ?>">Přidat novou flotilu</a>
		<a href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("Flotila:delete", [$flotila->FLOTILA_ID])) ?>">Vymazat flotilu</a>
<?php
		}
		if ($user->isInRole('Palpatine') || $user->getId() == $flotila->VELITEL) {
			?>		<a href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("edit", [$flotila->FLOTILA_ID])) ?>">Editovat flotilu</a>
        <a href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("Lode:register", [$flotila->VELITEL, $flotila->FLOTILA_ID])) ?>">Přidat loď do flotily</a>
<?php
		}
?>
</div>
<table class="show">
<tbody>
<tr>
	<th><label>ID flotily:</label></th>

	<td><label><?php echo LR\Filters::escapeHtmlText($flotila->FLOTILA_ID) /* line 21 */ ?></label></td>
</tr>

<tr>
	<th><label>Název flotily:</label></th>

	<td><label><?php echo LR\Filters::escapeHtmlText($flotila->NAZEV) /* line 27 */ ?></label></td>
</tr>

<tr>
	<th><label>Velitel flotily:</label></th>

	<td><label><?php echo LR\Filters::escapeHtmlText($flotila->JMENO) /* line 33 */ ?></label></td>
</tr>

<tr>
<?php
		if ($planeta) {
?>
        <th><label>Flotila se nachází na orbitu planety:</label></th>

        <td><label><?php echo LR\Filters::escapeHtmlText($planeta->NAZEV) /* line 40 */ ?></label></td>
<?php
		}
		else {
?>
        <th><label>Flotila se nenachází na orbitu žádné planety</label></th>
<?php
		}
?>
</tr>

<tr>
	<th><label>Počet členů flotily:</label></th>

	<td><label><?php echo LR\Filters::escapeHtmlText($flotila->POCET_CLENU) /* line 49 */ ?></label></td>
</tr>

<tr>
	<th><label>Počet lodí ve flotile:</label></th>

	<td><label><?php echo LR\Filters::escapeHtmlText($lode->POCET_LODI) /* line 55 */ ?></label></td>
</tr>

<tr>
	<th><label>Počet jedi ve flotile:</label></th>

	<td><label><?php echo LR\Filters::escapeHtmlText($pocet_jedi->POCET_JEDI) /* line 61 */ ?></label></td>
</tr><?php
	}


	function blockTitle($_args)
	{
		extract($_args);
		?><h1>Flotila <?php echo LR\Filters::escapeHtmlText($flotila->NAZEV) /* line 3 */ ?></h1>
<?php
	}

}
