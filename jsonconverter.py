from PIL import Image
import json
import time
import requests
import os
import mysql.connector
update_only=True
def toBool(x):
	if x == "legal":
		return "1"
	return "0"
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
# cursor = mydb.cursor()
newcardstring = ""
updatecardstring = ""
# https://api.scryfall.com/cards/search?q=type%3Aspell%20-is%3Afunny%20game%3Apaper
with open("oracle-cards.json", mode='r', encoding='utf8') as f:
	json_arr = json.load(f)
	for item in json_arr:
		try:
			if 				("Creature" in item['type_line'] \
							or "Instant" in item['type_line'] or "Sorcery" in item['type_line'] \
							or "Artifact" in item['type_line'] or "Enchantment" in item['type_line'] \
							or "Planeswalker" in item['type_line'] or "Battle" in item['type_line']) \
							and (item['legalities']['legacy'] == "legal" or item['legalities']['modern'] == "legal" \
							or item['legalities']['pioneer'] == "legal" or item['legalities']['commander'] == "legal"):
				obj = 	{	"name":item['name'].replace('/', '_').replace('"', '\\"'), 
							"colors":item["colors"], 
							"img":item['image_uris']['border_crop'],
							"legalities":item['legalities']
						}
				sql = 'INSERT INTO CARDS\
(name, colors, Standard, Pioneer, Modern, Legacy, Vintage, Commander, Pauper, Oathbreaker) VALUES \
(\"'+obj['name']+'\", \"'+toColors(obj['colors'])+'\", '+toBool(item['legalities']['standard'])+', ' \
+toBool(item['legalities']['pioneer'])+', '+toBool(item['legalities']['modern'])+', '+toBool(item['legalities']['legacy'])\
+', '+toBool(item['legalities']['vintage'])+', '+toBool(item['legalities']['commander'])+', '+toBool(item['legalities']['pauper'])\
+', '+toBool(item['legalities']['oathbreaker'])+');'
				filename = './images/'+item['name'].replace('/', '_').replace("'", "_").replace('"', '_').replace(':', '_')+'.jpg'; 
				if update_only and not os.path.isfile(filename):
					filename = './newimages/'+item['name'].replace('/', '_').replace("'", "_").replace('"', '_').replace(':', '_')+'.jpg'; 
					img_data = requests.get(obj['img']).content
					# Isolate new images to upload to server
					with open(filename, 'wb') as handler:
						handler.write(img_data)
					foo = Image.open('./newimages/'+item['name'].replace('/', '_').replace("'", "_").replace('"', '_').replace(':', '_')+'.jpg')
					foo.save('./newimages/'+item['name'].replace('/', '_').replace("'", "_").replace('"', '_').replace(':', '_')+'.jpg', optimize=True, quality=60)
					# SQL Inserts to update server database
					newcardstring +=sql+'\n'
					print("downloaded card:", filename)
					# cursor.execute(sql)
					time.wait(1)
				else:
					sql2 = 'UPDATE CARDS SET Standard='+toBool(item['legalities']['standard'])+\
					', Pioneer='+toBool(item['legalities']['pioneer'])+', Modern='+toBool(item['legalities']['modern'])+\
						', Legacy='+toBool(item['legalities']['modern'])+', Vintage='+toBool(item['legalities']['vintage'])+\
							', Commander='+toBool(item['legalities']['commander'])+', Pauper='+toBool(item['legalities']['pauper'])+\
								', Oathbreaker='+toBool(item['legalities']['oathbreaker'])+' WHERE name = \"'+obj['name']+'\";'
					updatecardstring+=(sql2+'\n')
					# cursor.execute(sql2)
		except:
			pass
with open('sqlupdates.txt', 'w') as sqlupdates:
	sqlupdates.write(newcardstring)
	sqlupdates.write(updatecardstring)
# mydb.commit()
# cursor.close()
out_file = open("min-cards.json", mode='w')
out_file.close()
# sqliteConnection.close()