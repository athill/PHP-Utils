<?php namespace Athill\Utils\Templates;
//namespace templates;
class DefaultTemplate extends \Athill\Utils\TemplateBase {

	protected function beginRender() {
		global $h;
		$h->odiv(['class'=>'container', 'id'=>'container']);
	}

	protected function endRender() {
		global $h;
		$h->cdiv('/#container');
	}

	protected function heading() {
		global $h, $site;
		$h->oheader(['id'=>'header']);
		$h->odiv(['class'=>'banner']);
		$h->h1($site['sitename']);
		$h->cdiv('/.banner');
		$h->cheader();
	}

	protected function beginLayout() {
		global $site, $h;
		$h->otags([
			['class'=>'container-fluid'],
			['class'=>'grid'],
			['id'=>'layout', 'class'=>'row']
		]);
		$leftsidebar = $site['layout']['leftsidebar'];
		$rightsidebar = $site['layout']['rightsidebar'];
		$contentcols = 12;
		if (count($leftsidebar) > 0) {
			$h->odiv(['class'=>'col-md-2']);
			$contentcols -= 2;
			$this->sidebar('left-sidebar', $leftsidebar);
			$h->cdiv();
		}
		if (count($rightsidebar) > 0) {
			$contentcols -= 2;
		}
		$h->odiv(['id'=>'content', 'class'=>'col-md-'.$contentcols]);
		$this->beginContent();
	}

	protected function beginContent() {
		global $site, $h;
		$h->h2($site['pagetitle']);
		$this->messages();
	}

	protected function endContent() {}

	protected function endLayout() {
		global $site, $h;
		$this->endContent();
		$h->cdiv('/#content');
		$rightsidebar = $site['layout']['rightsidebar'];
		if (count($rightsidebar) > 0) {
			$h->odiv(['class'=>'col-md-2']);
			$this->sidebar('right-sidebar', $rightsidebar);
			$h->cdiv();
		}
		$h->ctags([
			'/#layout',
			'/.grid',
			'/.container-fluid'
		]);
	}

	protected function footer() {
		global $h, $site;
		$h->ofooter();
		$h->tnl('&copy; '. $site['meta']['copyright']);
		$h->cfooter();
	}
}