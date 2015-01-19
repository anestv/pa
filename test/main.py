'''
Copyright 2014 Anestis Varsamidis
Licensed under the EUPL, Version 1.1 only (the "Licence");
You may not use this work except in compliance with the Licence.
You may obtain a copy of the Licence at:

https://joinup.ec.europa.eu/software/page/eupl
'''

import json, configparser
from myRequest import request

print('# Starting server-test...')

try:
  config = configparser.ConfigParser()
  config.read('settings.ini')
  s = config['Configuration']
except:
  s = {}

SERVER_ADDR = s.get('SERVER_ADDR', 'http://localhost')
PAGES_PATH = s.get('PAGES_PATH', '/')
TEST_CASES_FILE = s.get('TEST_CASES_FILE', 'testCases.json')
USE_SESSION = s.get('USE_SESSION', False) == 'True'
# == is to convert string 'True' to bool. We
# do not getboolean because s may be a dict

jsonString = open(TEST_CASES_FILE, 'r').read()

allRequests = json.loads(jsonString)

for req in allRequests:
  url = SERVER_ADDR + PAGES_PATH + req['page']
  
  reqObj = request(url, req.get('expect', {}), req.get('get', {}), req.get('post', {}))
  
  if USE_SESSION and 'session' in req:
    reqObj.session = int(req['session'])
  
  reqObj.send(USE_SESSION)

print('# All tests finished successfully! server-test closing...')
