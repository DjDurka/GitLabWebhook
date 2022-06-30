<?php
require __DIR__ . '/vendor/autoload.php';
use Symfony\Component\HttpClient\HttpClient;

$raw_data = file_get_contents('php://input');
$data = json_decode($raw_data, true);
$project_id = $data["project"]["id"];
$MR_id = $data["object_attributes"]["iid"];
$api_url = "https://gitlab.com/api/v4/projects/%s/merge_requests/%s/commits";
$fp = fopen("output.txt", "w");

$httpClient = HttpClient::create(['headers' => ['PRIVATE-TOKEN' => 'your token',]]);
$response = $httpClient->request('GET', sprintf($api_url, $project_id, $MR_id));
$raw_commits = $response->getContent();

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
fclose($fp);
?>