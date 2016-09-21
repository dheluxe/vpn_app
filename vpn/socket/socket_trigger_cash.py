#!/usr/bin/python
import time
import signal

from websocket import create_connection
ws = create_connection("ws://198.211.127.72:8880")

while True:
#   signal.alarm(1)
   time.sleep(60)
   ws.send("{\"message_type\":\"request\",\"type\":\"deduct_cash\"}")
   pass



result =  ws.recv()
print "Received '%s'" % result
ws.close()
