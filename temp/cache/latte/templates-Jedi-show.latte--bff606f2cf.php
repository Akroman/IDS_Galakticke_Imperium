<?php
// source: D:\PHP\IDS\app\Presenters/templates/Jedi/show.latte

use Latte\Runtime as LR;

class Templatebff606f2cf extends Latte\Runtime\Template
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
			?><a href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("Jedi:register")) ?>">Zaregistrovat nového Jedi</a><?php
		}
?>

<?php
		if ($user->isInRole('Palpatine')) {
			?>		<a href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("Jedi:edit", [$jedi->JEDI_ID])) ?>">Upravit profil</a>
		<a href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("Jedi:delete", [$jedi->JEDI_ID])) ?>">Smazat profil</a>
<?php
		}
?>
</div>

<table class="show">
<tbody>
<tr>
	<th><label>ID Jedi:</label></th>

	<td><label><?php echo LR\Filters::escapeHtmlText($jedi->JEDI_ID) /* line 18 */ ?></label></td>
</tr>

<tr>
	<th><label>Jméno:</label></th>

	<td><label><?php echo LR\Filters::escapeHtmlText($jedi->JMENO) /* line 24 */ ?></label></td>
</tr>

<tr>
	<th><label>Příjmení:</label></th>

	<td><label><?php echo LR\Filters::escapeHtmlText($jedi->PRIJMENI) /* line 30 */ ?></label></td>
</tr>

<tr>
	<th><label>Planeta původu:</label></th>

	<td><label><?php echo LR\Filters::escapeHtmlText($planeta->NAZEV) /* line 36 */ ?></label></td>
</tr>

<tr>
	<th><label>Flotila:</label></th>

	<td><label><?php echo LR\Filters::escapeHtmlText($flotila->NAZEV) /* line 42 */ ?></label></td>
</tr>

<tr>
	<th><label>Rasa:</label></th>

	<td><label><?php echo LR\Filters::escapeHtmlText($jedi->RASA) /* line 48 */ ?></label></td>
</tr>

<tr>
	<th><label>Množství midichlorianů:</label></th>

	<td><label><?php echo LR\Filters::escapeHtmlText($jedi->MNOZSTVI_CHLORIANU) /* line 54 */ ?></label></td>
</tr>

<tr>
	<th><label>Barva meče:</label></th>

	<td><label><?php echo LR\Filters::escapeHtmlText($jedi->BARVA_MECE) /* line 60 */ ?></label></td>
</tr>

<tr>
	<th><label>Datum narození:</label></label></th>

	<td><label><?php echo LR\Filters::escapeHtmlText($narozeni->NAROZENI) /* line 66 */ ?></label></td>
</tr>

<tr>
	<th><label>Jedi je padawanem:</label></th>

	<td><label><?php
		if ($jedi->JE_PADAWAN === 1) {
			?>Ano<?php
		}
		else {
			?>Ne<?php
		}
?></label></td>
</tr>

<tr>
	<th><label>Oprávnění Jedi:</label></th>

	<td><label><?php echo LR\Filters::escapeHtmlText($jedi->OPRAVNENI) /* line 78 */ ?></label></td>
</tr>

</tbody></table><?php
	}


	function blockTitle($_args)
	{
		extract($_args);
		?><h1>Profil Jedi <?php echo LR\Filters::escapeHtmlText($jedi->JMENO) /* line 3 */ ?> <?php echo LR\Filters::escapeHtmlText($jedi->PRIJMENI) /* line 3 */ ?></h1>
<?php
	}

}
