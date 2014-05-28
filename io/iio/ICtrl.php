<?php
namespace iio;
interface ICtrl {
	/* error: return false and print out warning info ; throw exception only when illeagle $val is passed in*/
	public function __set($name, $val);
	/* error: return false and print out warning info */
	public function __get($name);
	/* return true or false */
	public function __isset($name);
}
