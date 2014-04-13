
<?php
/*
	public methods :

		1. MapXml2Arr :: map ($xmlstr) ,   convert xml str into array, and return the result

		2. MapXml2Arr :: prepare ($xmlstr),  convert xml str into array , return null, but put the result in self::$array 

		3. MapXml2Arr :: get_xml_error (),  read xml parsing error

	public vars:
	
		1. MapXml2Arr :: array,   the result of converting an xml str

		2. MapXml2Arr :: parse_error,  boolean,  specify whether the conversion is succeful or failed


	this class works on the model like below:

		<opentag_1>
			[a]
			<opentag_2>
				...
			</opentag_2>
			[c]
		</opentag_1>

			
		[a]: something betweem two open tags , which  we name 'cdata_o2o', short for "cdata_opentag_2_opentag"

		[b]: which we name 'cdata', is either a string or another dom, like the dom <opentag_2> before

		[c]: something betweem two close tags, which we name 'cdata_c2c' , short for "cdata_closetag_2_closetag"

*/
class MapXml2Arr implements IMap {

	/* parse result  is put in $array */
	public  $array = array();


	/* parse error */
	public  $parse_error = false;


	/* the field name shown in $array
		$attr_tag,  used to specify the attributes of elements 
		$cdata_tag, used to specify the cdata of elements
		$cdata_o2o_tag, short for "cdata_opentag_2_opentag_tag" 
		$cdata_c2c_tag, short for "cdata_closetag_2_closetag_tag" 
	*/
	public  $attr_tag = 'attr';
	public  $cdata_tag = 'cdata';
	public  $cdata_o2o_tag = 'cdata_o2o';	/* tag for cdata betweem two open tag, like :  <body> something of cdata_o2o <p> ... */
	public  $cdata_c2c_tag = 'cdata_c2c';   /* tag for cdata betweem two close tag, like :  ....</p> something of cdata_c2c </body */


	/* whether to filter cdata_o2o and cdata_c2c or not */
	public  $filter_o2o_c2c = true;	/* filter the cdata betweem <opentag1> ... <opentag2> , like the string "i am here" of ' <body> i am here <p> ...' */


	/* with smart mode = true, 
	  	1.  the elements' attribute will be put in $arr[ MapXml2Arr::ATTR_TAG ], discard what the value of $attr_tag is 
		2.  elements without child elements will be parse without their attribues
		3.  elements named as "__attr" cann't not be 
	   	4.  there will be no $arr['cdata_tag'], all Child elements will be reference directly ,
		5.  elements array with only one element will be replace it's element
	
	   for example, say we have a xml string:
		<root class="body">
			<p>first one </p>
			<p>second one</p>
			<div><p>thrid one</p></div>
		</root>


		without smart mode, and filter_o2o_c2c = true,  it will parse into (we ignore the key word 'array' while specifing an  array):
		array ("root" => (
				"attr" => ("class"=>"body"),
				"cdata" => ( 
					"p" => (
						0 => ( "attr"=>(), "cdata"=>"first one")
						1 => ( "attr"=>(), "cdata"=>"second one")
					),
					"div" => (
						0 => ( "attr"=>(), "cdata" => (
							"p" => (
								0 => ( "attr"=>(), "cdata"=>"thrid one")
							)
						)
					)
								
				)
			)
		)

		while with smart mode, and filter_o2o_c2c = true, it will be parse like this:

		array("root" => (
			"__attr" => ("class"=>"body"),
			"p" => ( 
				0 => "first one",
				1 => "second one"
			)
			"div" =>(
				"p" => "third one"
			)
		)

	*/
	public  $smart_mod = true;
	private $last_closed_tag = null;
	const ATTR_TAG = '___attr';
	const CDATA_TAG = '___cdata';


	private $tag_stack = array();	/* ( tag, pointer, state ) */
	const TAG_ENTER = 1;
	const TAG_IN = 2;
	const TAG_LEAVE = 3;


	private $parser;


	public function map($xmlstr) {
		$this->prepare($xmlstr);
		if ($this->parse_error) {
			return array();
		} else {
			if ($this->smart_mod) {
				$this->array =  $this->array['__ROOT'][0];
			} else {
				$this->array =  $this->array['__ROOT'][0][$this->cdata_tag];
			}
			return $this->array;
		}
	}

	public function prepare($xml) {
		$this->parser = xml_parser_create("UTF-8");
		if ($this->smart_mod) {
		    $this->array = array(
			   '__ROOT' => array(
				0 => array()
			   ),
			);
		} else {
		    $this->array = array(
			    '__ROOT' => array(
				0 => array(
				    $this->attr_tag=>array(),
				    $this->cdata_tag=>array()
				    )
				)
			    );
		}
		array_push($this->tag_stack, array('tag'=>'__ROOT', 'pt'=>&$this->array['__ROOT'], 'i'=>0, 'st'=>self::TAG_ENTER));
		xml_set_object($this->parser, $this);
		xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, false);
		
		if ($this->smart_mod) {
		    xml_set_element_handler($this->parser, "smt_tag_open", "smt_tag_close");
		    xml_set_character_data_handler($this->parser, "smt_cdata");

		} else {
		    xml_set_element_handler($this->parser, "tag_open", "tag_close");
		    xml_set_character_data_handler($this->parser, "cdata");
		}
		$this->parse_error = xml_parse($this->parser, ltrim($xml))? false : true;
	}

	/** Get the xml error if an an error in the xml file occured during parsing. */
	public function get_xml_error() {
		if ($this->parse_error) {
			$errCode = xml_get_error_code ($this->parser);
			$thisError =  "Error Code [". $errCode ."] " . xml_error_string($errCode)." at char ".xml_get_current_column_number($this->parser) . " on line ".xml_get_current_line_number($this->parser)."";
		} else {
			$thisError = $this->parse_error;
		}
		return $thisError;
	}


	public function __destruct() { xml_parser_free($this->parser);}


	/* smat mod functions */


	private function smt_tag_open($parser, $tag, $attributes){
		$curr_tag = array_pop($this->tag_stack);
		$pointer = &$curr_tag['pt'][$curr_tag['i']];
		array_push($this->tag_stack, $curr_tag);

		/* auto merge neighbor element */
		if ($tag != $this->last_closed_tag 
			&& isset($pointer[$this->last_closed_tag])
				&& count($pointer[$this->last_closed_tag]) == 1) {
			$pointer[$this->last_closed_tag] = $pointer[$this->last_closed_tag][0];
		}

		if (isset($pointer[$tag]) && is_array($pointer[$tag])) {
			$new_no = count($pointer[$tag]);
		} else {
			$new_no = 0;
		}
		$pointer[$tag][$new_no] = array();
		array_push($this->tag_stack, 				/* save state info of the tag */
			array('tag'=>$tag, 'pt'=>&$pointer[$tag], 'i'=>$new_no, 'st'=>self::TAG_ENTER));

		if (!empty($attributes)) {
			$pointer[$tag][$new_no][self::ATTR_TAG] = $attributes;		/* set tag's attributes */
		} 
	}

	private function smt_cdata($parser, $cdata){
		$curr_tag = array_pop($this->tag_stack);
		$pointer = &$curr_tag['pt'][$curr_tag['i']];
		switch ($curr_tag['st']) {

			case self::TAG_ENTER:	
				$pointer[self::CDATA_TAG] = $cdata;			/* read the cdata_o2o */
				///echo $cdata . "--" . $curr_tag['tag'] . "\n";
				$curr_tag['st'] = self::TAG_IN;				/* change tag's state , to inform the nex call of cdata() */
				array_push($this->tag_stack, $curr_tag);		
				break;

			case self::TAG_IN:
				if (isset($pointer[self::CDATA_TAG])) {
			//		unset($pointer[self::CDATA_TAG]);
				}
				$curr_tag['st'] = self::TAG_LEAVE;		
				array_push($this->tag_stack, $curr_tag);		
				break;

			case self::TAG_LEAVE:
			default:
				/* do nothing */
				array_push($this->tag_stack, $curr_tag);		
				break;
				
		}
	}

	private function smt_tag_close($parser, $tag) {
		$curr_tag = array_pop($this->tag_stack);	
		$curr_pointer = &$curr_tag['pt'][$curr_tag['i']];

		/* merge self element , if the closing tag is an 'leaf node' */
		if (count($curr_pointer) == 1 && isset($curr_pointer[self::CDATA_TAG])) {
		    $curr_pointer = $curr_pointer[self::CDATA_TAG];

		} else if (isset($curr_pointer[$this->last_closed_tag])
			&& count($curr_pointer[$this->last_closed_tag]) == 1 )
		{
		    $curr_pointer[$this->last_closed_tag] = $curr_pointer[$this->last_closed_tag][0];
		    unset($curr_pointer[self::CDATA_TAG]);

		} else {
		    unset($curr_pointer[self::CDATA_TAG]);
		}

		$this->last_closed_tag = $tag;
	}

	private function tag_open($parser, $tag, $attributes) {
		$curr_tag = array_pop($this->tag_stack);
		$pointer = &$curr_tag['pt'][$curr_tag['i']][$this->cdata_tag];
		array_push($this->tag_stack, $curr_tag);

/*
		if ($this->smart_mod) {
*/

			/* smart mod shift , shift the neighbor element */
/*

			if ($tag != $this->last_closed_tag 
				&& isset($pointer[$this->last_closed_tag])
					&& count($pointer[$this->last_closed_tag]) == 1) {
				$pointer[$this->last_closed_tag] = $pointer[$this->last_closed_tag][0];
			}
			
		}
*/

		if (isset($pointer[$tag]) && is_array($pointer[$tag])) {
			$new_no = count($pointer[$tag]);
		} else {
			$new_no = 0;
		}
	
		$pointer[$tag][$new_no] = array();
		array_push($this->tag_stack, 				/* save state info of the tag */
			array('tag'=>$tag, 'pt'=>&$pointer[$tag], 'i'=>$new_no, 'st'=>self::TAG_ENTER));

		$pointer[$tag][$new_no][$this->attr_tag] = $attributes;		/* set tag's attributes */
		$pointer[$tag][$new_no][$this->cdata_tag] = array();		/* prepare for child doms */
	}

	/** Adds the current elements content to the current pointer[cdata] array. */
	private function cdata($parser, $cdata) {
		$curr_tag = array_pop($this->tag_stack);
		$pointer = &$curr_tag['pt'][$curr_tag['i']];
		switch ($curr_tag['st']) {

			case self::TAG_ENTER:	
				$pointer[$this->cdata_o2o_tag] = $cdata;		/* read the cdata_o2o */
				$curr_tag['st'] = self::TAG_IN;				/* change tag's state , to inform the nex call of cdata() */
				array_push($this->tag_stack, $curr_tag);		
				break;

			case self::TAG_IN:
				!$this->filter_o2o_c2c and ($pointer[$this->cdata_c2c_tag] = $cdata);	/* read the cdata_c2c */
				$curr_tag['st'] = self::TAG_LEAVE;		
				array_push($this->tag_stack, $curr_tag);		
				break;

			case self::TAG_LEAVE:
			default:
				/* do nothing */
				array_push($this->tag_stack, $curr_tag);		
				break;
				
		}
	 }

	private function tag_close($parser, $tag) {
		$curr_tag = array_pop($this->tag_stack);	
		$curr_pointer = &$curr_tag['pt'][$curr_tag['i']];

		/* this means that the tag has no child dom , and it's  'cdata_o2o' should be named as 'cdata' */
		if ($curr_tag['st'] == self::TAG_IN) {
			$curr_pointer[$this->cdata_tag] = $curr_pointer[$this->cdata_o2o_tag];
			unset($curr_pointer[$this->cdata_o2o_tag]);

			/*smart mod*/
/*
			if ($this->smart_mod) {

*/
				/* smart mod shift, shift the self element */
/*
				if (count($curr_pointer[$this->cdata_tag]) == 1) {
					$curr_tag['pt'][$curr_tag['i']] = $curr_pointer[$this->cdata_tag];
				} 
			}
*/

		} 

		/* smart mod shift, shift the children element */
/*
		 if (isset($curr_pointer[$this->cdata_tag][$this->last_closed_tag])
			&& count($curr_pointer[$this->cdata_tag][$this->last_closed_tag]) == 1 )
		{
		    $curr_pointer[$this->cdata_tag][$this->last_closed_tag] = $curr_pointer[$this->cdata_tag][$this->last_closed_tag][0];
		}
*/

		$this->last_closed_tag = $tag;
		if ($this->filter_o2o_c2c && isset($curr_pointer[$this->cdata_o2o_tag]) ) {
			unset($curr_pointer[$this->cdata_o2o_tag]);
		}
	}
}
