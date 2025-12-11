<?php
// Standalone Tester for Google Places API v2
// Usage: php scripts/test_google_v2.php

$api_key = 'AIzaSyDCdLTaU480FV1wGt4VLiZAyfG_YmIXLmM'; // User provided key
$keyword = 'Pizza';
$location = 'Chicago';

$url = 'https://places.googleapis.com/v1/places:searchText';

$body = array(
    'textQuery' => $keyword . ' in ' . $location,
    'maxResultCount' => 3
);

$field_mask = 'places.id,places.displayName,places.formattedAddress,places.priceLevel,places.rating';

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'X-Goog-Api-Key: ' . $api_key,
    'X-Goog-FieldMask: ' . $field_mask
));

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: " . $http_code . "\n";
echo "Response: " . $response . "\n";

if ($http_code == 200) {
    echo "\n✅ SUCCESS: Google Places v2 API is working!\n";
} else {
    echo "\n❌ FAILURE: Check API Key or Quota.\n";
}
