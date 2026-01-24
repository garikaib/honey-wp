import requests
from bs4 import BeautifulSoup
import os

url = "https://junglehouse.com.my/"
headers = {
    'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
}

print(f"Fetching {url}...")
response = requests.get(url, headers=headers)

if response.status_code == 200:
    soup = BeautifulSoup(response.content, 'html.parser')
    
    # Try to identify the header
    header = soup.find('header')
    if not header:
        print("No <header> tag found, looking for class='header'...")
        header = soup.find(class_=lambda x: x and 'header' in x.lower())

    if header:
        print("Header found!")
        
        # Save raw HTML of header
        with open('scraper/header.html', 'w') as f:
            f.write(header.prettify())
            
        print("Saved header HTML to scraper/header.html")
        
        # Extract menu items
        print("\n--- Menu Items ---")
        nav_items = header.find_all(['li', 'a'])
        for item in nav_items:
            text = item.get_text(strip=True)
            if text:
                print(f"Item: {text}")
        
        # Extract logo image
        logo = header.find('img')
        if logo:
            print(f"\nLogo src: {logo.get('src')}")
            
    else:
        print("Could not locate a clear header element.")
        
        # Save full page for manual inspection if needed
        with open('scraper/full_page.html', 'w') as f:
            f.write(soup.prettify())
else:
    print(f"Failed to fetch page. Status: {response.status_code}")
