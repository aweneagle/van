<?php
namespace iio;

/* buffered io
 */
interface IBuffer extends IIo{
	public function flush();
	public function clean();
}
