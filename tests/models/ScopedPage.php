<?php

namespace SelfComposer\PageTreeManager\Tests\Models;

use SelfComposer\PageTreeManager\PageNode;

class ScopedPage extends PageNode
{
	protected $table = 'pages';

	protected $scoped = array('type');
}