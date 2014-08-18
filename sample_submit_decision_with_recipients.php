<?php
require_once 'opendata.php';

// ΑΝΑΡΤΗΣΗ ΠΡΑΞΗΣ ΜΕ ΛΙΣΤΑ ΠΑΡΑΛΗΠΤΩΝ

$client = new OpendataClient();
$client->setAuth("10599_api", "User@10599");

$metadataJsonString = file_get_contents("SampleDecisionMetadata.json");
$metadata = json_decode($metadataJsonString, true);
$pdf = "SampleDecision.pdf";

$recipients = array("alice.jones@example.com", "bob.loblaw@example.com", "charly.saiz@example.com");

$response = $client->submitDecision($metadata, $pdf, null, $recipients);

if ($response->code === 200) {
    print "ΑΔΑ: " . $response->data['ada'];
} else {
    print "Error " . $response->code;
    print_r($response);
    if ($response->data !== null) {
        echo "Response: " . json_encode($response->data);
    }
}

?>