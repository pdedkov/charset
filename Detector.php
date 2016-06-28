<?php
namespace Charset;

use Config\Object as Base;

/**
 * Определение кодировки строки из возможных windows-1251, koi-8r, utf-8, iso-8859-1
 */
class Detector extends Base {
	protected static $_defaults = array(
		//Частотные таблицы символов для разных кодировок
		'encodings' => array(
			'windows-1251'	=> 'char_spec_w1251.php',
			'koi8-r'		=> 'char_spec_koi8.php',
			'iso-8859-1'	=> 'char_spec_iso8859.php'
		),
		'aliases'	=> array(
			'cp1251' => 'windows-1251',
			'win1251' => 'windows-1251',
			'windows1251' => 'windows-1251',
			'win-1251' => 'windows-1251',
			'cp-1251' => 'windows-1251',
			'utf8' => 'utf-8',
			'windows-1251' => 'windows-1251',
			'utf-8' => 'utf-8'

		)
	);

	/**
	 * Определяем кодировку строки
	 *
	 * @param string $string строка текста
	 *
	 * @return string название кодировки, empty - если значение не определено
	 */
    public function determCharset($string) {
		//экспресс-проверка на utf-8
    	if (preg_match('#.#u', $string) > 0) {
    		return 'utf-8';
    	}

    	//массив с рейтингом кодировок - числовое значение типа float, для каждой из 3х проверяемых кодировок
		$encRates = array();
		//проход по входящей строке, для каждой из кодировок инкремент на значение частоты для соответствующей кодировки и соответствующего символа
		for ($i = 0; $i < strlen($string); ++$i) {
			foreach ($this->_config['encodings'] as $encoding => $charSpecter) {
				$charSpecter = include $charSpecter;
				
				$index = ord($string[$i]);
				if (isset($charSpecter[$index])) {
					if (isset($encRates[$encoding])) {
						$encRates[$encoding] += $charSpecter[$index];
					} else {
						$encRates[$encoding] = $charSpecter[$index];
					}
				}
			}
		}

		//сравнение рейтингов кодировок, если 2е максимальные равны по рейтингу - то кодировку определить не удалось
		if ($encRates['windows-1251'] > $encRates['koi8-r']/2) {
			if ($encRates['windows-1251'] > $encRates['iso-8859-1']/2) {
				return 'windows-1251';
			} elseif ($encRates['windows-1251'] < $encRates['iso-8859-1']) {
				return 'iso-8859-1';
			}
		} elseif  ($encRates['windows-1251'] < $encRates['koi8-r']) {
			if ($encRates['koi8-r'] >= $encRates['iso-8859-1']) {
				return 'koi8-r';
			} elseif ($encRates['koi8-r'] < $encRates['iso-8859-1']) {
				return 'iso-8859-1';
			}
    	}
		// скорее всего это она, а там уже как пойдёт
    	return 'windows-1251';
    }

    /**
	 * Выдаем алиас к названию кодировки, если он определен.
	 *
	 * @param string $encoding название кодировки
	 * @return string алиас
	 */
    public function normalize($encoding) {
		$encoding = strtolower($encoding);

		$encAliases = $this->_config['aliases'];
		if (array_key_exists($encoding, $encAliases)) {
			$alias = $encAliases[$encoding];
			if (!empty($alias)) {
				return array($alias, $encoding);
			}
		}

		return null;
    }
}