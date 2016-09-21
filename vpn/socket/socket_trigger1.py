#!/usr/bin/python
import sys
import time
import signal
import threading
import socket
from threading import Thread
from websocket import create_connection

def func1():
  ws = create_connection("ws://198.211.127.72:8880")
  ws.send("{\"message_type\":\"request\",\"type\":\"deduct_cash\"}")
  pass

def func2():
  ws1 = create_connection("ws://198.211.127.72:8880")
  ws1.send("{\"message_type\":\"request\",\"type\":\"process_complete\"}")
  pass

print(sys.argv)
if sys.argv[1] == "0":
 func1()
else:
 func2()