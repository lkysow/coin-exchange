### To run
* Install vagrant: http://downloads.vagrantup.com/
* Install virtualbox: https://www.virtualbox.org/wiki/Downloads
* Install ruby and puppet (used for provisioning)
* `git clone https://github.com/lkysow/coin-exchange`
* `cd coin-exchange/vagrant`
* `vagrant up; vagrant provision`
  * `vagrant up` will provision, but there's a weird bug with installing zeromq over pecl that requires running `vagrant provision` again
* Go to http://localhost:9999/api_server.php/transactions?limit=100

### Architecture
The exchange runs on a front end php server that processes HTTP API requests and then communicates via inter-process communication with a continuously running php process that holds the state of the exchange. The continuously running process runs in a `while()` loop and accepts messages over IPC using ZeroMQ.

The API server is built using Silex which is a super light weight PHP framework inspired by Sinatra. The API server is run from `bin/api_server.php`. The routes are defined in `ApiController`.

The exchange server runs in `bin/exchange_loop.php` and uses the `Exchange` class to run the exchange.

### TODO's
* The data structures in the exchange can be much more efficient
* Sorting isn't stable so newer bids at one price could be resolved before older bids at the same price
* Floats are used instead of BigNumber or something of the like so the math can be inaccurate
* There is no persistence of the data (it's all in memory) which means
  * The system is fragile; if the process goes down the data is lost
  * The process will eventually run out of memory from holding the lists


### Server issues
There are two PHP processes running. One is a looped ZeroMQ server that accepts data over IPC and the other is the built in PHP server that's serving the API.
If you have any problems:
* `vagrant ssh` and `ps -ef | grep php`
You should see two php processes running:

```bash
root     12115     1  0 07:15 ?        00:00:00 php bin/exchange_loop.php
root     12126     1  0 07:15 ?        00:00:00 php -S 0.0.0.0:9999 -t bin
vagrant  12247 12146  0 07:15 pts/0    00:00:00 grep --color=auto php
```

You can restart both of these processes. Note that sudo is required.
```bash
vagrant ssh
cd /var/www
sudo php bin/exchange_loop.php
sudo php -S 0.0.0.0:9999 -t bin
```
