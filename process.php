<?php

ini_set('memory_limit', '64M');

require('./classes/xml.php');

$XML = new xml();

$xmlfile = file_get_contents($_FILES['that_file']['tmp_name']);

$XML->parse($xmlfile);

$old_data = $XML->getData();

unset($XML);

$newdata = Array();

//echo substr(0,4000,print_r($old_data,1));
//exit();

foreach($old_data['kml'][0]['folder'][0]['document'][0]['folder'][0]['placemark'] as $id => $data){
  $name = explode('[',trim($data['name'][0]['contents']));
  $newdata[$id]['name'] = trim($name[0]);
  $subname = explode('/',str_replace(Array('[',']'),'',$name[1]));
  $newdata[$id]['strength'] = trim($subname[0]);
  $newdata[$id]['security'] = trim($subname[1]);  
  $desc = explode(' ',trim($data['description'][0]['contents']));
  $newdata[$id]['mac'] = trim($desc[0]);
  $newdata[$id]['channel'] = trim(str_replace(Array('(Ch.',')'),'',$desc[1]));
  $point = explode(',',$data['point'][0]['coordinates'][0]['contents']);
  $newdata[$id]['latitude'] = $point[0];
  $newdata[$id]['longitude'] = $point[1];
}

//print_r($newdata);

echo "name,strength,security,mac,channel,latitude,longitude<br />\n";

foreach($newdata as $row){
  echo $row['name'].',';
  echo $row['strength'].',';
  echo $row['security'].',';
  echo $row['mac'].',';
  echo $row['channel'].',';
  echo $row['latitude'].',';
  echo $row['longitude']."<br />\n";
}












?>