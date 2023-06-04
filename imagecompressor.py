from PIL import Image
import os
DIR='images/'
for x in os.listdir(DIR):
	print(x)
	foo = Image.open(DIR+x)  
	# foo.size  # (480, 680)
	
	# downsize the image with an ANTIALIAS filter (gives the highest quality)
	# foo = foo.resize((400,600),Image.ANTIALIAS)
	
	# foo.save('path/to/save/image_scaled.jpg', quality=95)  # The saved downsized image size is 24.8kb
	
	foo.save(DIR+x, optimize=True, quality=60) 