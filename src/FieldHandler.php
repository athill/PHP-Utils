<?php  namespace Athill\Utils;

class FieldHandler {
	public $defs = array();
	public $data = array();
	public $appendColonToLabel = true;

	function __construct($defs, $data = array()) {
		$this->defs = $defs;
		$this->data = $data;
	}

	function renderField($field_id, $atts='') {
		global $h;
		$field = $this->getField($field_id);
		if (array_key_exists('fieldatts', $field)) {
			$atts = $this->addAtt($atts, $field['fieldatts']);
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
					$atts = $this->addAtt($atts, 'size="'.$field['size'].'"');
				}
				if (array_key_exists('maxlength', $field)) {
					$atts = $this->addAtt($atts, 'maxlength="'.$field['maxlength'].'"');
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
			default:
				# code...
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

	private function getField($field_id) {
		$field = [];
		//// field from array
		if (is_array($field_id)) {
			if (!isset($field_id['id'])) {
				throw new Exception('Id not defined in field_id');
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
				throw new Exception('Field_id not in defs');
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
			'fieldtype'=>'text',
			'value'=>''
		];
		foreach ($defaults as $k=>$v) {
			if (!isset($field[$k])) {
				$field[$k] = $v;
			}
		}
		return $field;
	}

	function getValue($field_id) {
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

	private function addAtt($atts, $att) {
		return ($atts == '') ? $att : $atts . ' '.$att;
	}	

	//// probably won't use these
	function inline($field_id) {
		$this->renderLabel($field_id);
		$this->renderField($field_id);
	}

	function inline_r($field_id) {
		$this->renderField($field_id);
		$this->renderLabel($field_id);
	}

	function twoline($field_id)	{
		global $h;
		$this->renderLabel($field_id);
		$h->br();
		$this->renderField($field_id);		
	}

	function row($field_id) {
		global $h;
		$h->oth();
		$this->renderLabel($field_id);
		$h->cth();
		$h->otd();
		$this->renderField($field_id);
		$h->ctd();
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