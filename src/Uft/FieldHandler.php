<?php  namespace Athill\Utils\Uft;

class FieldHandler {
	public $defs = [];
	public $data = [];
	public $appendColonToLabel = true;

	function __construct($defs, $data=[]) {
		$this->defs = $defs;
		$this->data = $data;
	}

	function renderField($field_id, $atts=[]) {
		global $h;
		$field = $this->getField($field_id);
		if (array_key_exists('fieldatts', $field)) {
			$atts = $h->addAtts($atts, $field['fieldatts']);
		}
		$value = $this->getValue($field_id);
		$type = $field['fieldtype'];
		switch ($type) {
			case 'submit':
			case 'intext':
			case 'phone':
			case 'date':
			case 'email':
			case 'number':
			case 'file':
			case 'password':
				$type = ($type == 'intext') ? 'text': $type;
				if (array_key_exists('size', $field)) {
					$atts = $h->addAtts($atts, ['size'=>$field['size']]);
				}
				if (array_key_exists('maxlength', $field)) {
					$atts = $h->addAtts($atts, ['maxlength'=>$field['maxlength']]);
				}
				if ($type == 'date') {
					$atts = $h->addClass($atts, 'datepicker');
				}
				$h->input($type, $field_id, $value, $atts);
				if($type =='file'){
					if ($value != '') {
						$h->tnl('Current: ' .$value);
						$h->input('checkbox', $field_id."__delete", 1);
						$h->label($field_id."__delete", "Delete");
						
					}
				}
				break;
			case 'checkbox':
				$h->input($type, $field_id, $value, $atts);
				break;
			case 'textarea':
				$h->textarea($field_id, $value, $atts);
				break;
			case 'editor':
				$h->editor($field_id, $value);
				break;					
			case 'select':
				$h->select($field_id, $field['options'], $value, $atts);
				break;
			case 'button':
				$atts = $h->addAtts(['name'=>$field_id, 'id'=>$field_id], $atts);
				$h->button($field['content'], $atts);
				break;
			default:
				throw new \Exception('Unimplemented fieldtype in FieldHandler: '.$type);
				break;
		}
	}

	function renderLabel($field_id, $atts='') {
		global $h;
		$label = array_key_exists('label', $this->defs[$field_id]) ? $this->defs[$field_id]['label'] : '';
		if ($label == '') {
			return;
		}
		if ($this->appendColonToLabel) {	
			$label .= ':';	
		}		
		$h->label($field_id, $label, $atts);
		$required = (array_key_exists('required', $this->defs[$field_id])) ? 
			$this->defs[$field_id]['required'] :
			false;
		if ($required) {
			$h->span('*', 'class="required"');
		}
	}

	protected function getField($field_id) {
		$field = [];

		//// field from array
		// var_dump($field_id);
		if (is_array($field_id)) {
			if (!isset($field_id['id'])) {
				throw new \Exception('Id not defined in field_id');
			} else {
				//// add to defs?
				$field = $field_id;
			}
		//// field from defs
		} else {
			if (isset($this->defs[$field_id])) {
				$field = $this->defs[$field_id];
				$field['id'] = $field_id;
			} else {
				throw new \Exception('Field_id not in defs');
			}
		}
		if (count($field) == 0) {
			throw new Exception('Field not initialized');
		}
		$field = $this->setFieldDefaults($field);
		return $field;
	}

	private function setFieldDefaults($field) {
		global $h;
		//// TODO: this should be more robust, set defaults per field type, etc.
		$defaults = [
			'label'=>'',
			'fieldtype'=>'intext',
			'value'=>''
		];
		foreach ($defaults as $k=>$v) {
			if (!isset($field[$k])) {
				$field[$k] = $v;
			}
		}
		return $field;
	}

	protected function getValue($field_id) {
		if (array_key_exists('value', $this->defs[$field_id])) {
			return $this->defs[$field_id]['value'];
		} else if (array_key_exists($field_id, $this->data)) {
			return $this->data[$field_id];	
		} else if (array_key_exists('defaultVal', $this->defs[$field_id])) {
			return $this->defs[$field_id]['defaultVal'];
		} else {
			return '';
		}
	}




	// function initField($fieldname) {
	// 	$options = array(
	// 		'required'=>array(),
	// 		'defaults'=>array(
	// 			'id'=>$fieldname,
	// 			'label'=>''
	// 		),
	// 		'fieldtypes'=>array(
	// 			'select'=>array(
	// 				'required'=>array('options'),
	// 				'defaults'=>array()
	// 			)

	// 		)
			

	// 	);
	// 	foreach ($options as $key => $value) {
	// 		# code...
	// 	}
	// }

}


?>