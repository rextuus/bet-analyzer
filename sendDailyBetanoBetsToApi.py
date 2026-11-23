import requests
import json

def main():
    # Read JSON data from file
    with open("betano_test_response.json", "r") as file:
        data = json.load(file)

    # Define the API endpoint URL
    url = "http://localhost:8000/api/betano/daily"

    try:
        # Make POST request to the API endpoint
        response = requests.post(url, json=data)
        response.raise_for_status()  # Raise an exception for 4xx or 5xx status codes

        print("Data successfully sent to Symfony API")
    except requests.exceptions.RequestException as e:
        print("Error:", e)

if __name__ == "__main__":
    main()
