#!/usr/bin/env python3
"""
Full Pipeline Test: Gathering -> Parsing -> Dispersing

This script demonstrates the complete data flow:
1. GATHER: Fetch real business data from Google Places API v2
2. PARSE: Transform API response into WordPress-compatible format
3. DISPERSE: Simulate WP import by writing to companies.json
"""

import requests
import json
import os
from datetime import datetime

# --- Configuration ---
API_KEY = 'AIzaSyDCdLTaU480FV1wGt4VLiZAyfG_YmIXLmM'
URL = 'https://places.googleapis.com/v1/places:searchText'
OUTPUT_FILE = 'wp-content/plugins/directory-core/data/companies.json'

# Search Parameters
NICHE = 'Plumbers'
CITY = 'Austin'

print("=" * 60)
print("AUTONOMOUS DATA PIPELINE TEST")
print("=" * 60)

# --- STEP 1: GATHER ---
print(f"\n[1/3] GATHERING: Fetching '{NICHE}' in '{CITY}' from Google Places...")

headers = {
    'Content-Type': 'application/json',
    'X-Goog-Api-Key': API_KEY,
    'X-Goog-FieldMask': 'places.id,places.displayName,places.formattedAddress,places.rating,places.userRatingCount,places.websiteUri,places.nationalPhoneNumber,places.priceLevel,places.editorialSummary'
}

data = {
    'textQuery': f'{NICHE} in {CITY}',
    'maxResultCount': 5
}

response = requests.post(URL, headers=headers, json=data)
print(f"   Status: {response.status_code}")

if response.status_code != 200:
    print(f"   ERROR: {response.text}")
    exit(1)

raw_places = response.json().get('places', [])
print(f"   ✅ Found {len(raw_places)} businesses")

# --- STEP 2: PARSE ---
print("\n[2/3] PARSING: Transforming to WordPress format...")

parsed_companies = []
for place in raw_places:
    name = place.get('displayName', {}).get('text', 'Unknown Business')
    address = place.get('formattedAddress', '')
    rating = place.get('rating', 0)
    review_count = place.get('userRatingCount', 0)
    website = place.get('websiteUri', '')
    phone = place.get('nationalPhoneNumber', '')
    summary = place.get('editorialSummary', {}).get('text', f'Professional {NICHE} service.')
    price_level = place.get('priceLevel', 'PRICE_LEVEL_MODERATE')
    
    # Map price level
    price_map = {
        'PRICE_LEVEL_FREE': '$',
        'PRICE_LEVEL_INEXPENSIVE': '$',
        'PRICE_LEVEL_MODERATE': '$$',
        'PRICE_LEVEL_EXPENSIVE': '$$$',
        'PRICE_LEVEL_VERY_EXPENSIVE': '$$$$'
    }
    price_str = price_map.get(price_level, '$$')
    
    # Create WP-compatible structure
    company = {
        "id": f"google-{place.get('id', 'unknown')}",
        "basics": {
            "name": name,
            "tagline": f"Rated {rating}/5 by {review_count} reviews",
            "description": summary,
            "headquarters": address,
            "website": website,
            "phone": phone
        },
        "pricing": {
            "model": "Service",
            "starter_price": price_str
        },
        "media": {
            "logo": ""  # Would be populated via image sideloading
        },
        "use_cases": [
            {"industry": NICHE}
        ],
        "meta": {
            "source": "google_places_v2",
            "fetched_at": datetime.now().isoformat(),
            "is_claimed": False
        }
    }
    parsed_companies.append(company)
    print(f"   → {name} ({price_str})")

    # AI Enrichment Simulation (Matching PHP Logic)
    print(f"     [AI] Generating rich description for {name}...")
    company['basics']['description'] = f"<!-- AIC_Engine Generated Content -->\n<h3>About {name}</h3>\n<p>{summary}</p>\n<p>AI Generated description content would go here...</p>"

# --- STEP 3: DISPERSE ---
print(f"\n[3/3] DISPERSING: Writing {len(parsed_companies)} companies to {OUTPUT_FILE}...")

# Read existing data if present
existing_data = []
if os.path.exists(OUTPUT_FILE):
    try:
        with open(OUTPUT_FILE, 'r') as f:
            existing_data = json.load(f)
        print(f"   Existing entries: {len(existing_data)}")
    except:
        pass

# Merge (avoid duplicates by ID)
existing_ids = {c.get('id') for c in existing_data}
new_entries = [c for c in parsed_companies if c['id'] not in existing_ids]
final_data = existing_data + new_entries

with open(OUTPUT_FILE, 'w') as f:
    json.dump(final_data, f, indent=2)

print(f"   ✅ Written! Total entries now: {len(final_data)}")

# --- SUMMARY ---
print("\n" + "=" * 60)
print("PIPELINE COMPLETE")
print("=" * 60)
print(f"Source:   Google Places API v2")
print(f"Query:    {NICHE} in {CITY}")
print(f"Gathered: {len(raw_places)} raw results")
print(f"Parsed:   {len(parsed_companies)} companies")
print(f"New:      {len(new_entries)} (duplicates skipped)")
print(f"Output:   {OUTPUT_FILE}")
print("\nThis data is now ready for WordPress import via:")
print("  WP Admin → Directory Settings → Data Importer")
