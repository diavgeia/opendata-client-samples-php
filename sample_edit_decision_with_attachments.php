<?php
require_once 'opendata.php';

// ΕΝΗΜΕΡΩΣΗ ΠΡΑΞΗΣ ΜΕ ΣΥΝΗΜΜΕΝΑ

$client = new OpendataClient();
$client->setAuth("10599_api", "User@10599");

$ada = urlencode("[ΑΔΑ]");

$metadataJsonString = file_get_contents("SampleDecisionMetadata.json");
$metadata = json_decode($metadataJsonString, true);
$metadata['subject'] = 'ΑΠΟΦΑΣΗ ΑΝΑΛΗΨΗΣ ΥΠΟΧΡΕΩΣΗΣ [ΔΙΟΡΘΩΣΗ ΠΡΑΞΗΣ ΜΕ ΣΥΝΗΜΜΕΝΟ]';

// Create attachments array. Each item is (file, mimetype, description)
$attachments = array(
    array("Attachment.docx",
          "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
          "This is an attachment"),
          
    array("Attachment.xlsx",
          "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
          "This is another attachment"),
);

$attachmentsToRemove = array();

$response = $client->editDecision($ada, $metadata, null, $attachments, $attachmentsToRemove);

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