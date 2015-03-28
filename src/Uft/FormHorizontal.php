<?php  namespace Athill\Utils\Uft;

Class FormHorizontal {
	private $defs;
	private $layout;
	private $fh;

	function __construct($defs, $layout) {
		$this->defs = $defs;
		$this->layout = $layout;
		$this->fh = new \Athill\Utils\Uft\FieldHandler($defs);
	}

	public function render($options=[]) {
		global $h;
		$defaults = [
			'leftcolwidth'=>2,
		];
		$options = $h->extend($defaults, $options);
		$leftcolwidth = $options['leftcolwidth'];
		if (!is_numeric($leftcolwidth) || $leftcolwidth > 10) {
			throw new Exception('Invalid "leftcolwidth" value. Numeric and less than 11');
		}
		$rightcolwidth = 12 - $leftcolwidth;
		if ($leftcolwidth + $rightcolwidth !== 12) {
			throw new Exception('bad addition');
		}
		$h->oform('', 'post', [ 'class'=>'form-horizontal' ]);
		foreach ($this->layout as $field) {
			$h->odiv([ 'class'=>'form-group' ]);
			$this->fh->renderLabel($field, ['class'=>'col-sm-'.$leftcolwidth.' control-label']);
			$h->odiv(['class'=>'col-sm-'.$rightcolwidth]);
			$this->fh->renderField($field);
			$h->cdiv();	//// field
			$h->cdiv(); //// form-group
		}
		$h->cform('/.form-horizontal');
	}
}