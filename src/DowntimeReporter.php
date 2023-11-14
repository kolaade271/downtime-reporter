<?php

namespace DowntimeReporter;

use GuzzleHttp\Client;
use GuzzleHttp\Promise;

class DowntimeReporter {
    private $serverCondition;
    private $disallowedHttpStatusCodes;

    public function __construct($serverCondition, $disallowedHttpStatusCodes = []) {
        $this->serverCondition = $serverCondition;
        $this->disallowedHttpStatusCodes = $disallowedHttpStatusCodes;
    }

    public function reportDowntime($appName, $apiEndpoint) {
        // Check if the server is not in a good position
        if (!$this->serverCondition) {
            return null; // Do nothing if the server is not in a good position
        }

        // Get the HTTP status code of the current script's resource
        $httpStatus = $this->getScriptHttpStatus();

        // Check if the HTTP status is in the disallowed list
        if (in_array($httpStatus, $this->disallowedHttpStatusCodes)) {
            return null; // Do nothing if the HTTP status is in the disallowed list
        }

        // Send the HTTP status code to the specified API endpoint asynchronously
        $this->sendPostRequestAsync($apiEndpoint, ['app_name' => $appName, 'status_code' => $httpStatus]);

        // Return the result without waiting for the response
        return ['app_name' => $appName, 'status_code' => $httpStatus];
    }

    private function sendPostRequestAsync($url, $data) {
        $client = new Client();

        // Use Guzzle's async request
        $promise = $client->postAsync($url, [
            'json' => $data,
        ]);

        // You can handle the promise here if needed
        $promise->then(
            function ($response) {
                // Handle the response if necessary
            },
            function ($reason) {
                // Handle the error if necessary
            }
        );
    }

    private function getScriptHttpStatus() {
        // Get the HTTP status code of the current script's resource
        return http_response_code();
    }
}
