import requests
import json
import sys

# User provided confirmation of this key
API_KEY = 'AIzaSyDCdLTaU480FV1wGt4VLiZAyfG_YmIXLmM'
URL = 'https://places.googleapis.com/v1/places:searchText'

headers = {
    'Content-Type': 'application/json',
    'X-Goog-Api-Key': API_KEY,
    'X-Goog-FieldMask': 'places.id,places.displayName,places.formattedAddress,places.priceLevel,places.rating'
}

data = {
    'textQuery': 'Pizza in Chicago',
    'maxResultCount': 3
}

print(f"Testing Google Places API v2 with Key: {API_KEY[:5]}...{API_KEY[-5:]}")

try:
    response = requests.post(URL, headers=headers, json=data)
    print(f"Status Code: {response.status_code}")
    
    if response.status_code == 200:
        result = response.json()
        print("\n✅ SUCCESS! Connection Established.")
        places = result.get('places', [])
        print(f"Found {len(places)} places:")
        for p in places:
            name = p.get('displayName', {}).get('text', 'Unknown')
            addr = p.get('formattedAddress', 'No Address')
            print(f" - {name} ({addr})")
    else:
        print("\n❌ FAILURE: API Error")
        print(response.text)
        
except Exception as e:
    print(f"\n❌ EXCEPTION: {e}")
