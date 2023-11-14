<?php

namespace DowntimeReporter;

class DowntimeReporter {
    private $serverCondition;
    private $disallowedHttpStatusCodes;

    public function __construct($serverCondition, $disallowedHttpStatusCodes = []) {
        $this->serverCondition = $serverCondition;
        $this->disallowedHttpStatusCodes = $disallowedHttpStatusCodes;
    }

    public function reportDowntime($url, $appName, $apiEndpoint) {
        // Check if the server is not in a good position
        if (!$this->serverCondition) {
            return null; // Do nothing if the server is not in a good position
        }

        // Get the HTTP status of the server
        $httpStatus = $this->getHttpStatus($url);

        // Check if the HTTP status is in the disallowed list
        if (in_array($httpStatus, $this->disallowedHttpStatusCodes)) {
            return null; // Do nothing if the HTTP status is in the disallowed list
        }

        $data = [
            'url' => $url,
            'app_name' => $appName,
            'status_code' => $httpStatus,
        ];

        $response = $this->sendPostRequest($apiEndpoint, $data);

        $decodedResponse = json_decode($response, true);

        // Check if the downtime report was unsuccessful
        if ($decodedResponse && isset($decodedResponse['error'])) {
           return $decodedResponse['error'];
        }

        return $decodedResponse;
    }

    private function getHttpStatus($url) {
        $headers = get_headers($url, 1);
        $httpStatus = isset($headers[0]) ? substr($headers[0], 9, 3) : null;
        return $httpStatus !== null ? (int)$httpStatus : null;
    }

    private function sendPostRequest($url, $data) {
        $options = [
            'http' => [
                'header'  => "Content-type: application/json\r\n",
                'method'  => 'POST',
                'content' => json_encode($data),
            ],
        ];

        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        return $result;
    }
}
