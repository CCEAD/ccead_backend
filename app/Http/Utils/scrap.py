from bs4 import BeautifulSoup
import requests
import sys, json

url = sys.argv[1]
http = json.loads(json.dumps(url).replace("\\", ""))
endpoint = "http://" + http

req = requests.get(endpoint)
soup = BeautifulSoup(req.text, "html.parser")
result = []

for string in soup.find('table').strings:
    result.append(string.replace(u'\xa0', u''))

print(json.dumps(result))