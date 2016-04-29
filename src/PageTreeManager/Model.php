<?php
namespace PageTreeManager;

use Illuminate\Support\Facades\Config;

abstract class Model extends \Baum\Node {

	public function getTreeTable()
	{
		return Config::get('page-tree-manager.tree_table');
	}
}
