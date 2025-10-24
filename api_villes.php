<?php
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *");

if (!isset($_GET["code_postal"]) || !preg_match("/^\d{5}$/", $_GET["code_postal"])) {
    die(json_encode(array("success" => false, "message" => "Code postal invalide")));
}

$code_postal = $_GET["code_postal"];
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://geo.api.gouv.fr/communes?codePostal=" . urlencode($code_postal));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0");
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code !== 200) {
    die(json_encode(array("success" => false, "message" => "Erreur API")));
}

$data = json_decode($response, true);
if (!is_array($data)) {
    die(json_encode(array("success" => false, "message" => "Réponse invalide")));
}

$villes = array();
foreach ($data as $commune) {
    if (isset($commune["nom"])) {
        $villes[] = $commune["nom"];
    }
}

if (empty($villes)) {
    die(json_encode(array("success" => false, "message" => "Aucune ville trouvée")));
}

sort($villes);
die(json_encode(array("success" => true, "code_postal" => $code_postal, "villes" => $villes)));
