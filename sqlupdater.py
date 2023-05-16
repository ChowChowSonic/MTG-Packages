import json
import time
import requests
import os
import mysql.connector
update_only=True
lst=[]
names = []

def toColors(colorset):
	color_comb = ""
	for x in ["W", "U", "B", "R", "G"]:
		if x in colorset:
			color_comb+=x
		else:
			color_comb+='O'
	return color_comb

mydb = mysql.connector.connect(
  host="localhost",
  user="root",
  password="",
  database="mtgpackages"
)
cursor = mydb.cursor()

# https://api.scryfall.com/cards/search?q=type%3Aspell%20-is%3Afunny%20game%3Apaper
with open("oracle-cards.json", mode='r', encoding='utf8') as f:
	json_arr = json.load(f)
	for item in json_arr:
		try:
			if "paper" in item['games'] and item['name'] not in names and \
							("Creature" in item['type_line'] or "Instant" in item['type_line'] or "Sorcery" in item['type_line'] \
							or "Artifact" in item['type_line'] or "Enchantment" in item['type_line'] or "Planeswalker" in item['type_line'] \
							or "Battle" in item['type_line']) \
							and (item['legalities']['legacy'] == "legal" or item['legalities']['modern'] == "legal" \
	  						or item['legalities']['pioneer'] == "legal" or item['legalities']['commander'] == "legal"):
				
				obj = 	{	"name":item['name'].replace('"', '\\"'), 
							"colors":item["colors"], 
							"img":item['image_uris']['border_crop'],
							"legalities":item['legalities']
						}
				sql = 'INSERT INTO `cards`(name, colors, ramp, draw, tutor, removal, boardwipe, wincon, stax) VALUES (\"'+obj['name']+'\", \"'+toColors(obj['colors'])+'\", 0,0,0,0,0,0,0);'
				cursor.execute(sql)
				lst.append(obj)
				names.append(obj['name'])
		except:
			pass
mydb.commit()
cursor.close()
print(len(lst))
out_file = open("min-cards.json", mode='w')
json.dump(lst, out_file)
out_file.close()
# sqliteConnection.close()