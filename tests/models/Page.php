<?php

namespace SelfComposer\PageTreeManager\Tests\Models;

use SelfComposer\PageTreeManager\PageNode;

class Page extends PageNode
{
	protected $parentColumn = 'parent_id';

	protected $leftColumn = 'lft';

	protected $rightColumn = 'rgt';

	protected $depthColumn = 'depth';
}