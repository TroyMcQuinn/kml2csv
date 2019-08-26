<?php

class xml{

  var $data = Array(); // Array to contain parsed data.
  var $parent_tags = Array(); // Tracks current tag name hierarchy
  var $tag_counts = Array();
  var $depth = 0;
  var $parser; // Placeholder for internal XML parser object
  var $pointer; // Placeholder for addressing different levels of the data array
  var $char_data = ''; // Placeholder for character data

  // Parses XML data into a multi-dimensional array.
  function parse($data){    
    $this->parser = xml_parser_create('ISO-8859-1');
    xml_parser_set_option($this->parser,XML_OPTION_SKIP_WHITE,1);
    xml_set_object($this->parser,$this);
    xml_set_element_handler($this->parser,'xmlStartElement','xmlEndElement');
    xml_set_character_data_handler($this->parser,'xmlCharacterData');
    xml_parse($this->parser,$data,true);  
  }
  
  // Element start handler
  function xmlStartElement($parser,$name,$attributes){
    $name = strtolower($name); 
    $this->parent_tags[$this->depth] = $name;   
    if(isset($this->tag_counts[$this->depth][$name])){
      $this->tag_counts[$this->depth][$name]++;
    } else {
      $this->tag_counts[$this->depth][$name] = 0;
    }
    $this->depth++;
    $this->setPointer(); // Set pointer to the current tag name on the current level      
    $new_attributes = Array();
    foreach($attributes as $n => $v){
      $new_attributes[strtolower($n)] = $v;
    }
    if(count($new_attributes) > 0){
      $this->pointer['attributes'] = $new_attributes;
    }
  }
  
  // Element end handler
  function xmlEndElement($parser,$name){
    $name = strtolower($name);      
    unset($this->parent_tags[$this->depth]);
    if($this->char_data != ''){
      $this->pointer['contents'] = $this->char_data;
    }
    $this->char_data = '';
    $this->setPointer();
    unset($this->tag_counts[$this->depth]);
    $this->depth--;
  }
  
  // Character data handler
  function xmlCharacterData($parser,$data){
    $data = trim($data);
    $data = str_replace(Array('&lt;','&gt;','<','>'),'',$data);
    $this->char_data .= $data;
  }
  
  // Sets the data array pointer
  function setPointer(){   
    $eval_string = '';
    $a = '';
    foreach($this->parent_tags as $tag_depth => $tag_name){
      $a .= '[\''.$tag_name.'\']['.$this->tag_counts[$tag_depth][$tag_name].']';
    }
    $eval_string .= 'if(!isset($this->data'.$a.')){$this->data'.$a.' = Array();}'."\n";
    $eval_string .= '$this->pointer = &$this->data'.$a.';';    
    eval($eval_string);
  }
  
  // Retrieves the parsed data
  function getData(){
    return $this->data;
  }
  
}

?>