import json
import time
import requests
import os
import pyodbc
db_insert=False
download= True 
names=[]
# sqliteConnection = pyodbc.connect(connstring="Server=localhost;Database=mtgpackages;Uid=root;Pwd=;").cursor()
# https://api.scryfall.com/cards/search?q=type%3Aspell%20-is%3Afunny%20game%3Apaper
with open("oracle-cards.json", mode='r', encoding='utf8') as f:
	json_arr = json.load(f)
	for item in json_arr:
		try:
			if "paper" in item['games'] and "Creature" in item['type_line']\
	  					and "Human" in item["type_line"] and item['colors'] == ["R"]:
				names.append(item['name'])
			# elif "Land" in item['type_line']:
			# 	print(item['name'])
		except:
			pass
with open("Cards_list.txt", 'w', encoding='utf8') as out:
	for x in names:
		out.write(x+'\n')
print(len(names))
# sqliteConnection.close()