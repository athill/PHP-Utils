# PHP Utility Classes

Work in progress at this point. These are classes that I've copied and pasted and modified in various projects. Trying to abstract out things that are site specific, so they can be used generally.

## Update 2015-02-14
I've just touched the surface, but am rendering a basic, compliant HTML page. To run the demo:

1. You need [PHP](http://php.net/), [Composer](https://getcomposer.org/), and [Bower](http://bower.io/). Install these.
2. Download this repo (unzip, cd into)
3. cd into demo. demo will probably soon be a separate repository, it's convenient for me to have it all in one directory, but many complications and benefits point toward moving it.
4. $ composer update
5. $ bower install
6. $ php -S localhost:8000
7. Go to [localhost:8000](http://localhost:8000) in your browser, view source
8. win

The actual point of this is in the demo/vendor/athill/php-utils/src directory. Much of it in not being used at this point. Currently, here's what happens:

1. You go to [localhost:8000](http://localhost:8000)
2. As you haven't requested a specific page, you get index.php, which looks like this
	
	<?php
	require_once('setup.inc.php');

	$page = new \Athill\Utils\Page();

	$h->p('content');

	$page->end();
3. setup.inc.php sets some things up and calls Setup.php which sets a bunch of defaults.
4. Page.php uses these defaults and optional options to render the stuff around the content
5. The content, in this case, is &lt;p&gt;content&lt;/p&gt;, from $h->p('content');
