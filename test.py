import requests
import json

def main():
    url_betano = "https://www.betano.de/api/sport/fussball/anstehende-spiele-heute/?sort=StartTime"
    url_symfony_api = "https://bet-analyzer.wh-company.de/api/betano/daily"

    headers = {
        "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/98.0.4758.102 Safari/537.36"
    }

    try:
        # Get JSON data from Betano API
        response_betano = requests.get(url_betano, headers=headers)
        response_betano.raise_for_status()  # Raise an exception for 4xx or 5xx status codes
        data_betano = response_betano.json()

        # Make POST request to Symfony API endpoint
        response_symfony = requests.post(url_symfony_api, json=data_betano)
        response_symfony.raise_for_status()  # Raise an exception for 4xx or 5xx status codes

        print("Data successfully sent to Symfony API")
    except requests.exceptions.RequestException as e:
        print("Error:", e)

if __name__ == "__main__":
    main()