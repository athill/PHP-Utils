<?php  namespace Athill\Utils\Uft;

Class FormHorizontal {
	private $defs;
	private $layout;
	private $buttons;
	private $fh;

	function __construct($config) {
		$this->defs = $config['defs'];
		$this->layout = $config['layout'];
		$this->buttons = (isset($config['buttons'])) ? $config['buttons'] : [];
		$this->fh = new \Athill\Utils\Uft\FieldHandler($this->defs);
	}

	public function render($options=[]) {
		global $h;
		$defaults = [
			'leftcolwidth'=>2,
		];
		$options = $h->extend($defaults, $options);
		$leftcolwidth = $options['leftcolwidth'];
		if (!is_numeric($leftcolwidth) || $leftcolwidth > 10) {
			throw new \Exception('Invalid "leftcolwidth" value. Numeric and less than 11');
		}
		$rightcolwidth = 12 - $leftcolwidth;
		if ($leftcolwidth + $rightcolwidth !== 12) {
			throw new \Exception('bad addition');
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
		if (count($this->buttons) > 0) {
			// $h->pa($this->buttons);
			$h->odiv([ 'class'=>'form-group' ]);
			$h->odiv('class="col-sm-offset-'.$leftcolwidth.' col-sm-'.$rightcolwidth.'"');
			foreach ($this->buttons as $button) {
				$this->fh->renderField($button);
			}
			$h->cdiv();
			$h->cdiv(); //// form-group	
		}
		$h->cform('/.form-horizontal');
	}
}