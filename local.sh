#!/bin/bash

docker run --rm -it -p 80:80 -p 443:443 -v $(PWD):/var/www/default -v /tmp:/tmp -v ~/chat:/var/www/chat -v ~/.aws/:/.aws/ --name minible buildapart.io:latest
