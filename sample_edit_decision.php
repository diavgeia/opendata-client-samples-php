<?php
require_once 'opendata.php';

// ΕΠΕΞΕΡΓΑΣΙΑ ΜΕΤΑΔΕΔΟΜΕΝΩΝ ΠΡΑΞΗΣ

$client = new OpendataClient();
$client->setAuth("10599_api", "User@10599");

$ada = urlencode("[ΑΔΑ]");

$metadataJsonString = file_get_contents("SampleDecisionMetadata.json");
$metadata = json_decode($metadataJsonString, true);
$metadata['subject'] = 'ΑΠΟΦΑΣΗ ΑΝΑΛΗΨΗΣ ΥΠΟΧΡΕΩΣΗΣ [ΔΙΟΡΘΩΣΗ ΠΡΑΞΗΣ]';

$response = $client->editDecision($ada, $metadata);

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