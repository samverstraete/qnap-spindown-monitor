#!/usr/bin/env python3

import sys, paramiko, requests, time
import datetime
import os, daemon, signal
from daemon import pidfile

# monitor variables
address = "0.0.0.0"
username = "admin"
password = "password"
remotefile = "/var/ledvalue"
# upload variables
url = "http://address/upload.php"
# general variables
logfile = "/var/log/gemini-monitor.log"

from gemini_monitor_settings import * #real variabeles

if os.stat(logfile).st_size > 5000000:
	os.remove(logfile) 
log = open(logfile, 'w+')
	
def setup():
	global sftp, trans
	client = paramiko.SSHClient()
	client.load_system_host_keys()
	client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
	trans = paramiko.Transport((address, 22))
	trans.connect(None, username, password)
	sftp = paramiko.SFTPClient.from_transport(trans)
	
def close(signum, frame):
	sftp.close()
	trans.close()
	sys.exit(0)

def getLedState():
	f_in = sftp.file(remotefile, "r")
	currvalue = f_in.read()
	f_in.close()
	now = datetime.datetime.now()
	print (now.strftime("%Y-%m-%d %H:%M:%S"))
	print(currvalue)
	return currvalue

def uploadLedValue(value):
	params = {"state": value, "time": time.time()} #add time to prevent server-side caching
	headers = {"User-Agent": "Mozilla/5.0 (Linux) Apollo Chrome/39.0.2171.95", "Cache-Control": "no-cache", "Pragma": "no-cache"}
	response = requests.get(url, params = params, headers = headers)
	print(response.text)

def run():
	setup()
	while True:
		currvalue = '0x00090000' #default 9
		needsreconnect = False
		
		try:
			currvalue = getLedState()
		except OSError as err:
			print("OSError: {0}".format(err))
			needsreconnect = True
			time.sleep(60)
			
		try:
			uploadLedValue(currvalue)
		except ConnectionError as err:
			print("ConnectionError: {0}".format(err))
			time.sleep(60)
			
		if needsreconnect:
			setup()
		time.sleep(30)


if __name__ == "__main__":
	with daemon.DaemonContext(working_directory='/tmp', umask=0o002,
		pidfile=pidfile.TimeoutPIDLockFile('/var/run/' + os.path.basename(__file__) + '.pid'),
		signal_map={
            signal.SIGTERM: close,
            signal.SIGTSTP: close
        }, stdout=log, stderr=log) as context:
		run()

