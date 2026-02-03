import requests
import json

email = "gbdzoma@gmail.com"
api_key = "c387a52124c3ece44c4c4e36a2964a152e86a"
zone_id = "9948b0f9a5b77d81a57e9d6ed63f1956" # honeyscoop.co.zw

headers = {
    "X-Auth-Email": email,
    "X-Auth-Key": api_key,
    "Content-Type": "application/json"
}

records = [
    {
        "type": "TXT",
        "name": "sign._domainkey.crystalcred.co.zw",
        "content": "v=DKIM1; k=rsa; p=MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCx4Nb1V6Tt1ya42eHvGjG03nDiSBH3/riCS8yDKjo4hCdii/5i5NoNBWX7OeoJwP9VgdeyKJM0KUyfPt7GJXQ3LhHnKbZ0enk+oo6Japr9D1uG12n/Zify5IS8GYmJsgXzN2YrDH2SOmoeU5TXArWphjSU9u8BQ0d4LjdR8feluQIDAQAB",
        "ttl": 120,
        "comment": "DKIM for crystalcred"
    },
    {
        "type": "TXT",
        "name": "crystalcred.co.zw",
        "content": "v=spf1 include:mxsspf.sendpulse.com mx ip4:51.77.222.232 ip6:2001:41d0:305:2100::8406 ~all",
        "ttl": 120,
        "comment": "SPF for crystalcred"
    }
]

def add_records():
    url = f"https://api.cloudflare.com/client/v4/zones/{zone_id}/dns_records"
    
    for record in records:
        try:
            # Check if exists first to avoid dupes? Cloudflare allows dupes sometimes or errors.
            # We'll just try to create.
            response = requests.post(url, headers=headers, json=record)
            data = response.json()
            if data['success']:
                print(f"Successfully added record: {record['name']}")
            else:
                print(f"Failed to add record {record['name']}: {data['errors']}")
        except Exception as e:
            print(f"Request failed for {record['name']}: {e}")

if __name__ == "__main__":
    add_records()
