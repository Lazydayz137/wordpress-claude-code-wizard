#!/usr/bin/env python3
"""
Test Autonomous Metro Orchestrator
Simulating what happens when a user clicks "Launch Metro Campaign".
"""
import time
import random

CITY = "Austin, TX"
NICHES = ['Plumbers', 'Electricians', 'Roofers', 'Dentists']

print(f"🚀 LAUNCHING METRO CAMPAIGN FOR: {CITY}")
print("="*60)

total_businesses = 0
total_emails = 0

for niche in NICHES:
    print(f"\n[ORCHESTRATOR] Processing Niche: {niche}...")
    
    # 1. Fetch (Simulation)
    found = random.randint(3, 8)
    print(f"   via Google Places ... Found {found} listings.")
    total_businesses += found
    
    # 2. Enrich & Outreach
    for i in range(found):
        # Simulate processing time
        time.sleep(0.1)
        
        # Simulate Outreach
        if random.random() > 0.3:
            print(f"   [OUTREACH] 📧 Drafted & Sent email to Business #{i+1}")
            total_emails += 1
        else:
            print(f"   [OUTREACH] ❌ No contact info for Business #{i+1}")

print("\n" + "="*60)
print("CAMPAIGN COMPLETE")
print(f"Total Listings Created: {total_businesses}")
print(f"Total Emails Sent:      {total_emails}")
print("="*60)
