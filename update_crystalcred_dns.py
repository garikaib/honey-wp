import requests
import json

email = "garikaib@gmail.com"
api_key = "5f2e114ea312d7fe910251b60f62e43ff892f"
zone_id = "c80ff13cd6fb728b36c57d124b128f4c"

headers = {
    "X-Auth-Email": email,
    "X-Auth-Key": api_key,
    "Content-Type": "application/json"
}

# Records to set
target_spf = "v=spf1 include:mxsspf.sendpulse.com mx ip4:51.77.222.232 ip6:2001:41d0:305:2100::8406 ~all"
target_dkim = "v=DKIM1; k=rsa; p=MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCx4Nb1V6Tt1ya42eHvGjG03nDiSBH3/riCS8yDKjo4hCdii/5i5NoNBWX7OeoJwP9VgdeyKJM0KUyfPt7GJXQ3LhHnKbZ0enk+oo6Japr9D1uG12n/Zify5IS8GYmJsgXzN2YrDH2SOmoeU5TXArWphjSU9u8BQ0d4LjdR8feluQIDAQAB"

def get_records():
    url = f"https://api.cloudflare.com/client/v4/zones/{zone_id}/dns_records?type=TXT"
    response = requests.get(url, headers=headers)
    return response.json().get('result', [])

def update_record(record_id, data):
    url = f"https://api.cloudflare.com/client/v4/zones/{zone_id}/dns_records/{record_id}"
    response = requests.put(url, headers=headers, json=data)
    return response.json()

def create_record(data):
    url = f"https://api.cloudflare.com/client/v4/zones/{zone_id}/dns_records"
    response = requests.post(url, headers=headers, json=data)
    return response.json()

def main():
    existing_records = get_records()
    
    # Process SPF
    spf_found = False
    for r in existing_records:
        if r['name'] == 'crystalcred.co.zw' and r['content'].startswith('v=spf1'):
            print(f"Found existing SPF record: {r['content']}")
            print("Updating SPF record...")
            res = update_record(r['id'], {
                "type": "TXT",
                "name": "crystalcred.co.zw",
                "content": target_spf,
                "ttl": 120
            })
            print("Update result:", res['success'])
            spf_found = True
            break
            
    if not spf_found:
        print("No SPF record found. Creating new...")
        res = create_record({
            "type": "TXT",
            "name": "crystalcred.co.zw",
            "content": target_spf,
            "ttl": 120
        })
        print("Create result:", res['success'])

    # Process DKIM
    dkim_found = False
    for r in existing_records:
        if r['name'].startswith('sign._domainkey'):
            print(f"Found existing DKIM record: {r['name']}")
            print("Updating DKIM record...")
            res = update_record(r['id'], {
                "type": "TXT",
                "name": "sign._domainkey",
                "content": target_dkim,
                "ttl": 120
            })
            print("Update result:", res['success'])
            dkim_found = True
            break
            
    if not dkim_found:
        print("No DKIM record found. Creating new...")
        res = create_record({
            "type": "TXT",
            "name": "sign._domainkey",
            "content": target_dkim,
            "ttl": 120
        })
        print("Create result:", res['success'])

if __name__ == "__main__":
    main()
