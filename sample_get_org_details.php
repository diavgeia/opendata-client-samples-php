<?php
require_once 'opendata.php';

// ΕΚΤΥΠΩΣΗ ΠΛΗΡΟΦΟΡΙΩΝ ΦΟΡΕΑ (UID, ΕΠΩΝΥΜΙΑ, ΜΟΝΑΔΕΣ, ΥΠΟΓΡΑΦΟΝΤΕΣ)

$client = new OpendataClient();
$response = $client->getResource('/organizations/10599/details');
if ($response->code === 200) {    
    $orgData = $response->data;
    
    print $orgData['uid'] . ": " . $orgData['label'] . "\n";
    
    print "\nUnits:\n";
    foreach ($orgData['units'] as $unit) {
        print $unit['uid'] . ": " . $unit['label'] . "\n";
    }
    
    print "\nSigners:\n";
    foreach ($orgData['signers'] as $signer) {
        print $signer['uid'] . ": " . $signer['lastName'] . " " . $signer['firstName'] . "\n";
    }
    
} else {
    echo "Error " . $response->code;
}

?>