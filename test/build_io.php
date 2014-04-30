<?php 
	function echo_arr(array $arr, $tab_width=1){
		$tab = '';
		for ($i=0 ; $i < $tab_width; $i ++) {
			$tab .= "\t";
		}
		$echo = '';
		$echo .= 'array(' . "\n";
		foreach ($arr as $key => $val) {
			if (is_string($val)) {
				$echo .=  $tab . '"' . $key . '" => "' . $val . "\",\n";
			} else if (is_array($val)) {
				$echo .= $tab . '"' . $key . '" => ' . echo_arr($val, $tab_width + 1) . ",\n";
			}
		}
		$echo = trim(trim($echo, "\n"), ",");
		$echo .= ")";
		return $echo;
	}

	function obj_define(array $node) {
		$link =  $node[MapXml2Arr::ATTR_TAG]['link'];
		$class =  $node[MapXml2Arr::ATTR_TAG]['class'];
		unset($node[MapXml2Arr::ATTR_TAG]);

		echo "define('" . $link . "', '" . $link ."'); \n";

		$node['CLASS'] = $class;
		$echo_arr = echo_arr($node);
		$cfgstr = '$__tmpioarr = ' . $echo_arr . ";\n";


		$cfgstr .= '$__tmpioarr = array (' . $link . ' => $__tmpioarr );' . "\n";

		echo $cfgstr;


		echo 'van_batch_link( $__tmpioarr ); '."\n";

		echo 'unset($__tmpioarr);' . "\n";
	}


	include '../van.php';
	//van_set_error_report( VAN_ERROR_REPORT_WARNING );

	$io_xml = './etc/io.xml';

	van_assert(file_exists($io_xml), "file not exists", $io_xml);
	
	$xml = new MapXml2Arr();
	$arr = $xml->map(file_get_contents($io_xml));

	echo "<?php \n";
	foreach ($arr['root'][0]['io'] as $i => $node) {
	    $ini_str = obj_define($node);
	    echo $ini_str . "\n";
	}
