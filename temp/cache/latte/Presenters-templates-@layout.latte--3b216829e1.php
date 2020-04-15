<?php
// source: D:\PHP\IDS\app\Presenters/templates/@layout.latte

use Latte\Runtime as LR;

class Template3b216829e1 extends Latte\Runtime\Template
{
	public $blocks = [
		'scripts' => 'blockScripts',
	];

	public $blockTypes = [
		'scripts' => 'html',
	];


	function main()
	{
		extract($this->params);
?>
<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" href="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 4 */ ?>/IDS.css">
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width">

	<link rel="shortcut icon" type="image/x-icon" href="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 8 */ ?>/star-wars-png-icons-1.png">

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

	<title>Galaktické impérium</title>
</head>

<body>
<?php
		$iterations = 0;
		foreach ($flashes as $flash) {
			?>	<div id="flash"<?php if ($_tmp = array_filter(['alert-' . $flash->type])) echo ' class="', LR\Filters::escapeHtmlAttr(implode(" ", array_unique($_tmp))), '"' ?>>
		<?php echo LR\Filters::escapeHtmlText($flash->message) /* line 17 */ ?><button class="flashButton" aria-label="Close Account Info Modal Box">
		<?php
			if ($flash->type === 'error') {
				?>&#x274C;<?php
			}
			elseif ($flash->type === 'success') {
				?>&#x274E;<?php
			}
?></button>
	</div>
<?php
			$iterations++;
		}
?>
	<div class="topnav">
	<a href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("Homepage:")) ?>">Hlavní strana</a>
<?php
		if ($user->loggedIn) {
			?>		<a href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("Jedi:")) ?>">Jedi</a>
		<a href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("Flotila:")) ?>">Flotily</a>
		<a href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("PlanetarniSystem:")) ?>">Planetární systémy</a>
		<a style="float:right" href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("Sign:out")) ?>">Odhlásit se</a>
		<a style="float:right" href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("Jedi:edit", [$user->getId()])) ?>">Editovat profil</a>
		<label style="float:right">Přihlášen jako: <?php echo LR\Filters::escapeHtmlText($user->getIdentity()->name) /* line 28 */ ?></label>
<?php
		}
		else {
			?>		<a href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("Sign:")) ?>">Přihlásit se</a>
<?php
		}
?>
	</div>
<?php
		$this->renderBlock('content', $this->params, 'html');
?>

<?php
		if ($this->getParentName()) return get_defined_vars();
		$this->renderBlock('scripts', get_defined_vars());
?>

	<script type="text/javascript">
		$(document).ready(function() {
			$(".flashButton").click(function() {
				$("#flash").fadeOut();
			});
		});
	</script>
</body>
</html>
<?php
		return get_defined_vars();
	}


	function prepare()
	{
		extract($this->params);
		if (!$this->getReferringTemplate() || $this->getReferenceType() === "extends") {
			if (isset($this->params['flash'])) trigger_error('Variable $flash overwritten in foreach on line 16');
		}
		Nette\Bridges\ApplicationLatte\UIRuntime::initialize($this, $this->parentName, $this->blocks);
		
	}


	function blockScripts($_args)
	{
?>	<script src="https://nette.github.io/resources/js/3/netteForms.min.js"></script>
<?php
	}

}
