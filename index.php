<?php

require 'vendor/autoload.php';

use DowntimeReporter\DowntimeReporter;

// Set the server condition (true for demonstration purposes)
$serverCondition = true;

// Set the disallowed HTTP status codes (e.g., 404, 403)
$disallowedHttpStatusCodes = [404, 403];

// Create an instance of DowntimeReporter
$downtimeReporter = new DowntimeReporter($serverCondition, $disallowedHttpStatusCodes);

// Specify the URL, app name, and get the API endpoint from the configuration file
$url = 'http://example.com';
$appName = 'Example App';
$config = include 'config.php';
$apiEndpoint = $config['api_endpoint'];

// Report downtime only if the server is not in a good position
$response = $downtimeReporter->reportDowntime($url, $appName, $apiEndpoint);

// Check the response and take further action if needed
if ($response !== null) {
    // Downtime report was unsuccessful, take further action
    // For example, throw an exception or log the error
    // throw new \Exception('Downtime report failed: ' . $response['error']);
    // or
    // error_log('Downtime report failed: ' . $response['error']);
    echo "Downtime reported!\n";
} else {
    // Downtime report was successful or not required
    echo "No downtime reported.\n";
}
