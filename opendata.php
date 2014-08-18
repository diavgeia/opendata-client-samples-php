<?php

// HTTP_Request (http://pear.php.net/manual/en/package.http.http-request.php)
require_once 'HTTP/Request.php';

class ApiResponse {
    public $code = 200;
    public $data = null;
    
    function __construct($code, $data) {
        $this->code = $code;
        $this->data = $data;
    }
}

class OpendataClient {    
    public function __construct() {
        $this->baseUrl = 'https://test3.diavgeia.gov.gr/luminapi/opendata';
        $this->resetAuth();
    }
    
    public function setBaseUrl($url) {
        $this->baseUrl = $url;
        $this->auth = FALSE;
        $this->username = null;
        $this->password = null;
    }
    
    public function setAuth($username, $password) {
        $this->auth = TRUE;
        $this->username = $username;
        $this->password = $password;
    }
    
    public function resetAuth() {
        $this->auth = FALSE;
        $this->username = null;
        $this->password = null;
    }
    
    public function getResource($resource) {
        $req = new Http_Request($this->baseUrl . $resource, array('method'=> HTTP_REQUEST_METHOD_GET));
        $req->addHeader('Connection', 'Keep-Alive');
        $req->addHeader('Accept', 'application/json');
        if (!PEAR::isError($req->sendRequest())) {
            $responseCode = $req->getResponseCode();
            $responseBody = $req->getResponseBody();
            $data = null;
            if ($responseCode === 200) {
                $data = json_decode($responseBody, true);
            } 
            return new ApiResponse($responseCode, $data);
        } else {
            throw new Exception("Error while getting resource $resource");
        }
    }
    
    public function submitDecision($metadata, $pdf, $attachments=array(), $recipients=array()) {            
        $req = $this->_preparePostRequest('/decisions');
        
        if ($recipients !== null && (sizeof($recipients) > 0)) {
            $metadata = $this->_addRecipients($metadata, $recipients);
        }
        
        // Add metadata and decision document
        $req->addPostData('metadata', json_encode($metadata));
        $req->addFile('decisionFile', $pdf, 'application/pdf' );
        
        if ($attachments !== null && (sizeof($attachments) > 0)) {
            $this->_addAttachments($req, $attachments);
        }
        
        $result = $req->sendRequest();
        $err = PEAR::isError($result);

        if (!$err) {
            $responseCode = $req->getResponseCode();
            $responseBody = $req->getResponseBody();
            $data = null;
            if ($responseCode === 200 || $responseCode === 400) {
                $data = json_decode($responseBody, true);
            } 
            return new ApiResponse($responseCode, $data);
        } else {
            print $err;
            throw new Exception("Error while submitting decision");
        }
    }
    
    public function editDecision($ada, $metadata, $pdf=null) {
        if ($ada == null) {
            throw new Exception("The ADA must be specified");
        }
        
        $req = $this->_preparePostRequest('/decisions/' . $ada);
        
        $metadataJsonString = json_encode($metadata);
        if ((array_key_exists('correctedCopy', $metadata)) && ($metadata['correctedCopy'] === TRUE)) {
            $req->addFile('decisionFile', $pdf, 'application/pdf' );
            $req->addPostData('metadata', $metadataJsonString);
        } else {
            $req->addHeader('Content-Type', 'application/json');
            $req->setBody($metadataJsonString);
        }
        
        if (!PEAR::isError($req->sendRequest())) {
            $responseCode = $req->getResponseCode();
            $responseBody = $req->getResponseBody();
            $data = null;
            if ($responseCode === 200 || $responseCode === 400) {
                $data = json_decode($responseBody, true);
            } 
            return new ApiResponse($responseCode, $data);
        } else {
            throw new Exception("Error while editing decision");
        }
    }
    
    public function submitRevocationRequest($ada, $comment) {
        if ($ada == null OR $comment == null) {
            throw new Exception("The ADA and the comment must be specified");
        }
        
        $req = $this->_preparePostRequest('/decisions/requests/revocations');
        
        $requestData = array(
            "ada" => $ada,
            "comment" => $comment
        );
        
        $requestDataJsonString = json_encode($requestData);
        
        $req->addHeader('Content-Type', 'application/json');
        $req->setBody($requestDataJsonString);
        
        if (!PEAR::isError($req->sendRequest())) {
            $responseCode = $req->getResponseCode();
            $responseBody = $req->getResponseBody();
            $data = null;
            if ($responseCode === 200 || $responseCode === 400) {
                $data = json_decode($responseBody, true);
            } 
            return new ApiResponse($responseCode, $data);
        } else {
            throw new Exception("Error while submitting revocation request");
        }
    }
    
    
    // PRIVATE ////////////////////
    
    
    private function _preparePostRequest($urlPart) {
        if ($this->auth === TRUE) {
            $req = new Http_Request($this->baseUrl . $urlPart, array(
                'method' => HTTP_REQUEST_METHOD_POST,
                // set 'useBrackets' to false to avoid using the name 'attachments[]' instead of the accepted 'attachments'
                'useBrackets' => FALSE));
            
            $req->addHeader('Connection', 'Keep-Alive');
            $req->addHeader('Accept', 'application/json');
            $req->setBasicAuth($this->username, $this->password);
            
            return $req;
        } else {
            throw new Exception("You must authenticate to be able to submit decisions");
        }
    }
    
    private function _addAttachments($req, $attachments=array()) {
        if ($attachments !== null) {
            $attachmentFilenames = array();
            $attachmentContentTypes = array();
            $attachmentDescriptions = array();
            
            foreach ($attachments as $att) {
                array_push($attachmentFilenames, $att[0]);
                array_push($attachmentContentTypes, $att[1]);
                array_push($attachmentDescriptions, $att[2]);
            }
            
            $req->addFile('attachments', $attachmentFilenames, $attachmentContentTypes);
            $req->addPostData('attachmentDescr', json_encode($attachmentDescriptions));
        }
    }
    
    private function _addRecipients($metadata, $recipients=array()) {
        if ($recipients !== null) {
            $metadata['actions'] = array(
                array("name"=>"notifyRecipients", "args"=>$recipients)
            );
        }
        return $metadata;
    }
}


?>