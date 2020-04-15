<?php
// source: D:\PHP\IDS\app\Presenters/templates/Jedi/delete.latte

use Latte\Runtime as LR;

class Template784cd4e0cc extends Latte\Runtime\Template
{

	function main()
	{
		extract($this->params);
		return get_defined_vars();
	}


	function prepare()
	{
		extract($this->params);
		Nette\Bridges\ApplicationLatte\UIRuntime::initialize($this, $this->parentName, $this->blocks);
		
	}

}
