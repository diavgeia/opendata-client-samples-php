<?php
require_once 'opendata.php';

// ΕΚΤΥΠΩΣΗ ΤΥΠΩΝ ΠΡΑΞΕΩΝ (ΚΩΔΙΚΟΙ ΚΑΙ ΤΙΤΛΟΙ)

$resource = '/types';

$client = new OpendataClient();
$response = $client->getResource('/types');
if ($response->code === 200) {
    foreach($response->data['decisionTypes'] as $type) {
        print $type['uid'] . ": " . $type['label'] . "\n";
    }
} else {
    echo "Error " . $response->code;
}

?>