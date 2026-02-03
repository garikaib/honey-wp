import requests
import json

email = "garikaib@gmail.com"
api_key = "5f2e114ea312d7fe910251b60f62e43ff892f"
headers = {
    "X-Auth-Email": email,
    "X-Auth-Key": api_key,
    "Content-Type": "application/json"
}

def list_zones():
    url = "https://api.cloudflare.com/client/v4/zones?name=crystalcred.co.zw"
    try:
        response = requests.get(url, headers=headers)
        response.raise_for_status()
        data = response.json()
        if data['success']:
            print("Zones found:")
            for zone in data['result']:
                print(f"ID: {zone['id']} - Name: {zone['name']}")
        else:
            print("Error listing zones:", data['errors'])
    except Exception as e:
        print(f"Request failed: {e}")

if __name__ == "__main__":
    list_zones()
