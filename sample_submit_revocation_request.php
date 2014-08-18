<?php
require_once 'opendata.php';

// ΥΠΟΒΟΛΗ ΑΙΤΗΜΑΤΟΣ ΑΝΑΚΛΗΣΗΣ

$client = new OpendataClient();
$client->setAuth("10599_api", "User@10599");

$ada = "[ΑΔΑ]";
$comment = "[ΑΙΤΙΟΛΟΓΙΑ]";

$response = $client->submitRevocationRequest($ada, $comment);

if ($response->code === 200) {
    print "OK";
} else {
    print "Error " . $response->code;
    print_r($response);
    if ($response->data !== null) {
        echo "Response: " . json_encode($response->data);
    }
}

?>