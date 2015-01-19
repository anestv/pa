server-test
===========

## What's this?

This is a script in Python, made to be used in CI environments to test
projects that are "operated by servers". An example where it could be used
is this CI pseudo-configuration

* install the server and database
* start the server and database
* load server and database configuration
* run tests with server-test (`python main.py`)
* stop the server and database


## How to use it

### settings.ini explained:
```
[Configuration]

SERVER_ADDR = http://httpbin.org
PAGES_PATH = /
TEST_CASES_FILE = testCases-example.json
USE_SESSION = False
```

* `SERVER_ADDR`: The address of the server where the requests will be sent.
Currently ony one server is supported at a time. Default: `http://localhost`
* `PAGES_PATH`: The base path for the requested pages. Default: `/`
* `TEST_CASES_FILE`: The name of the file that contains the requests to
be sent to the server. Must be valid JSON. Default: `testCases.json`
* `USE_SESSION`: Whether to use sessions (send cookies) with the requests
or not. Default: `False`

All fields are optional. Even the file is optional.

### [testCases.json](testCases-example.json) explained:

It is a JSON file, consisted by an array of 'request' objects.
Below is the structure of a typical 'request' object:

* `page`: The only required property. A string (can be empty), the path of
the page where the request will be sent. It is relative to `PAGES_PATH`
* `get`: An object or string containing the data that will be added
as query parameters. Default: `{}`
* `post`: An object or string containing the data that will be sent as
the request body. If not empty, the request will be POST. *Note: "" and {}
are considered empty, so use `" "` to force an 'empty' POST.* Default: `{}`
* `expect`: An object, the HTTP headers and response code that are expected
in the response. The header names (keys) are case-insensitive. If a specified
header does not exist in the response or is different to the expected value
(case-sensitive), the script will exit with exit code 1. The special key
`status` is the expected HTTP code of the response. Default: `{"status": 200}`
* `session`: An integer, the number of the session which should be used to
send the request. If you don't care about using more than one sessions at a
time, omit this. Effective only if `USE_SESSIONS` is True. Default: `0`

## Contributing

You are welcome to create an pull request to fix something, or an issue
to suggest a new feature or report a bug.

## License

Copyright 2014 Anestis Varsamidis  
Licensed under the EUPL, Version 1.1 only (the "Licence");
You may not use this work except in compliance with the Licence.
You may obtain a copy of the Licence at:
https://joinup.ec.europa.eu/software/page/eupl

Unless required by applicable law or agreed to in writing,
software distributed under the Licence is distributed on an "AS IS" basis,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the Licence for the specific language governing permissions and limitations under the Licence.


Using this script to test your code is considered use (and not modification
of source), thus you don't have to license your work under a Compatible Licence

Library ["requests"](http://python-requests.org/) by Kenneth Reitz is included,
licensed under the Apache License, Version 2.0
