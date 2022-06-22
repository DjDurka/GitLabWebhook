<?php
$raw_data = file_get_contents('php://input');
$data = json_decode($raw_data, true);
$project_id = $data["project"]["id"];
$MR_id = $data["object_attributes"]["iid"];

$ch = curl_init("https://gitlab.com/api/v4/projects/{$project_id}/merge_requests/{$MR_id}/commits");
$fp = fopen("output.txt", "w");
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_HTTPHEADER, array("PRIVATE-TOKEN: <your token>"));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

if(curl_error($ch)) {
  fwrite($fp, curl_error($ch));
}

$raw_commits = curl_exec($ch);
$commits = json_decode($raw_commits, true);
$results = [];
foreach ($commits as $value){
  $title = $value["title"];
  preg_match("/^[a-zA-Z0-9._-]+-[0-9]+ /", $title, $matches);
  array_push($results, $matches[0]);
}
$results = array_unique($results);
for($i = 0, $size = count($results); $i < $size; ++$i){
  fwrite($fp, $results[$i]. "\r\n");
}
curl_close($ch);
fclose($fp);
?>
