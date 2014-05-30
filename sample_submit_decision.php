<?php
require_once 'opendata.php';

// ΑΝΑΡΤΗΣΗ ΠΡΑΞΗΣ

$client = new OpendataClient();
$client->setAuth("10599_api", "User@10599");

$metadataJsonString = file_get_contents("SampleDecisionMetadata.json");
$metadata = json_decode($metadataJsonString);
$pdf = "SampleDecision.pdf";

$response = $client->submitDecision($metadata, $pdf);

if ($response->code === 200) {
    print "ΑΔΑ: " . $response->data['ada'];
} else {
    print "Error " . $response->code;
    if ($response->data !== null) {
        echo "Response: " . json_encode($response->data);
    }
}

?>