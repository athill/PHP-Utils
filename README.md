# PHP Utility Classes

## Philosophy
The idea of this project is to be the the poor man's [Laravel](http://laravel.com/). By that I mean, I am a huge fan of Laravel, but 

1. Using Laravel (or another MVC (Model-View-Controller) framework) can be daunting if not impossible in a shared hosting environment 
2. As simple as Laravel makes things for the experienced programmer, there's still a pretty steep learning curve for a PHP novice. 
3. Even if you are experienced and can advance to a more mature framework, I hope these utilities can be helpful in transitioning from a legacy web site

Do not use this framework to write the next Twitter. It's meant for little websites of small organizations or hobbies.

The framework is meant to be small, intelligible, and extensible. It is easy to extend or replace the utilities, assuming the replacements fulfill the contract of the existing utility.

Also, heavily influenced by the concept of [convention over configuration](http://en.wikipedia.org/wiki/Convention_over_configuration).

## Page Flow
A page might look like this:

	<?php
	require('./setup.inc.php');

	$local = [
		///// override defaults
	];
	$page = new \Athill\Utils\Page($local);
	//// page content
	$page->end();

What's happening here? 

1. setup.inc.php is where you overide default settings found in Setup.php. It also starts/continues the session, sets the timezone, etc. It also creates global $h and $site variables. More on these later.
2. You could skip to "//// page content" at this point. Once you've loaded setup, you have an array (heh) of data and methods. However, it's up to you to supply any header or footer, etc.
3. The $local variable (just a convention) is where you override defaults for the current page. You can now override defaults on the site and page level
4. $page takes care of everything but the page content. Head tag, header, footer, optional sidebars. Passing a different template into $page can completely change the look and feel of the page.
5. $site is where the configuration is stored and available to you. 
6. $h is an HTML generator. For example:

		$h->div('content', ['class'=>'rad']);	//// div tag
		//// and 
		$h->odiv(['class'=>'rad']);				//// open div tag
		$h->tnl('content'); 					//// tab-newline
		$h->cdiv();								//// close div tag

		//// both generate <div class="rad">content</div>

	There are more complicated, but useful, methods, but you are under no obligation to use $h. Simply end your PHP tag after instantiating $page, reinstate it before calling $page->end(), and place your HTML content in between.
		
		<?php
		require('./setup.inc.php');

		$local = [
			///// override defaults
		];
		$page = new \Athill\Utils\Page($local);
		?>
		<!-- page content -->
		<?php
		$page->end();
		?>

## Status
Work in progress at this point.

### Update 2015-03-29
Basic setup and template system in place. Example implementation at [demo.anyhill.us](http://demo.andyhill.us). Code at [PHP-Utils-Demo](https://github.com/athill/PHP-Utils-Demo). To try it out, you either need a [vagrant box](https://www.vagrantup.com/) or the requirements in the previous update. Running the limited demo page of this repository requires only PHP and Composer

	> git clone https://github.com/athill/PHP-Utils
	> cd PHP-Utils
	> composer update
	> php -S localhost:8000
	//// go to localhost:8000 in your web browser

### Update 2015-02-14
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
