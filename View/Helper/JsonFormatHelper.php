<?php
App::uses('AppHelper', 'View/Helper');

class JsonFormatHelper extends AppHelper {

/**
 * _settings
 *
 * @var array
 */
	protected $_settings = array(
		'indent' => '  '
	);

/**
 * Allow overriding of settings
 *
 * @param View $View The View this helper is being attached to.
 * @param array $settings Configuration settings for the helper.
 */
	public function __construct(View $View, $settings = array()) {
		$this->_settings = array_merge($this->_settings, $settings);
		parent::__construct($View, $settings);
	}

/**
 * Indents a flat JSON string to make it more human-readable.
 *
 * @param string $json The original JSON string to process.
 * @link http://recursive-design.com/blog/2008/03/11/format-json-with-php/
 * @return string Indented version of the original JSON string.
 */
	public function format($json) {
		if (is_array($json)) {
			return $this->formatArray($json);
		}
		$return = '';
		$length = strlen($json);
		$prevChar = '';
		$pos = 0;
		$outOfQuotes = true;

		for ($i = 0; $i <= $length; $i++) {
			$char = substr($json, $i, 1);

			if ($char === '"' && $prevChar != '\\') {
				$outOfQuotes = !$outOfQuotes;
			} elseif (($char === '}' || $char === ']') && $outOfQuotes) {
				$return .= "\n";
				$pos --;
				for ($j = 0; $j < $pos; $j++) {
					$return .= $this->_settings['indent'];
				}
			}

			$return .= $char;

			if (($char === ',' || $char === '{' || $char === '[') && $outOfQuotes) {
				$return .= "\n";
				if ($char === '{' || $char === '[') {
					$pos ++;
				}

				for ($j = 0; $j < $pos; $j++) {
					$return .= $this->_settings['indent'];
				}
			}
			$prevChar = $char;
		}

		return $return;
	}

/**
 * formatArray
 *
 * Will only work with php 5.4. Mostly just a reminder
 *
 * @param mixed $json
 * @return string Indented version of the original JSON string.
 */
	public function formatArray($json) {
		if (defined("JSON_PRETTY_PRINT")) {
			return json_encode($json, JSON_PRETTY_PRINT);
		}
		return json_encode($json);
	}
}
