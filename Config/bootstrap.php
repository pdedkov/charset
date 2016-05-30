<?php
//Частотные таблицы символов для разных кодировок
Configure::write('Charset.encodings', [
	'windows-1251'	=> require APP . 'Vendor' . DS . 'char_spec_w1251.php',
	'koi8-r'				=> require APP . 'Vendor' . DS . 'char_spec_koi8.php',
	'iso-8859-1'		=> require APP . 'Vendor' . DS . 'char_spec_iso8859.php'
]);