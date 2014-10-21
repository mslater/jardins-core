import sys
from ftplib import FTP

ftp = None

def main():
	filename = sys.argv[1]
	print ("uploading: %s" % filename)
	try:
		if ftp == None:
		   connect()
		fp = open(filename, 'r')
		ftp.storlines('STOR %s' % filename , fp)
		print ('success')
	except Exception as e:
		print ("failed")
		print (e)
	finally:
		ftp.close()

def connect():
	global ftp
	ftp = FTP('ftp.jardins-sans-secret.com')
	if 'debug' in sys.argv:
		ftp.set_debuglevel(2)
	ftp.login('tarin@jardins-sans-secret.com', ';*-LtKBVG2aB')

if __name__ == '__main__':
	main()


