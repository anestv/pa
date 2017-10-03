from external import requests as reqs

class request:
  
  sessions = {0: reqs.Session()} # static
  
  def __init__(self, url, expect = {}, get = {}, post = {}):
    
    self.url = url
    self.get = get
    self.post = post
    self.expect = expect
    self.kind = 'POST' if post else 'GET'
    self.session = 0
  
  def checkExpects(self, r):
    
    if 'status' in self.expect:
      expStatus = self.expect['status']
      del self.expect['status']
    else:
      expStatus = 200
    
    if r.status_code != expStatus:
      print(self.kind + ' request to ' + self.url + ' responded with '
            + str(r.status_code) + ' instead of ' + str(expStatus))
      exit(1)
    
    for expHeader in self.expect:
      if not expHeader in r.headers:
        print('No header "' + expHeader + '" found in response to '
              + self.kind + ' request to ' + self.url)
        exit(1)
      
      if r.headers[expHeader] != self.expect[expHeader]:
        print('Header "' + expHeader + '" in ' + self.kind + ' request to ' + self.url
              + ' was not '+ self.expect[expHeader] + ' but ' + r.headers[expHeader])
        exit(1)
    
    print(' - ' + self.kind + ' request to ' + self.url + ' successful')
  
  def send(self, USE_SESSION):
    
    if USE_SESSION:
      if not self.session in self.sessions:
        self.sessions[self.session] = reqs.Session()
      
      _reqs = self.sessions[self.session]
    else:
      _reqs = reqs
    
    #possibly argument custom headers
    
    if self.kind == 'POST':
      r = _reqs.post(self.url, params = self.get, data = self.post)
    else:
      r = _reqs.get(self.url, params = self.get)
    
    self.checkExpects(r)
  
