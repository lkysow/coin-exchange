Exec { 
  path => [ "/usr/bin/", "/bin"],
}

exec { "apt-update":
    command => "/usr/bin/apt-get update",
}

package { "python-software-properties":
  ensure => installed,
  require => Exec['apt-update']
}

exec { "add-php5-repo":
  command => "/usr/bin/add-apt-repository ppa:ondrej/php5",
  require => Package["python-software-properties"]
}

exec { "apt-update-2":
  command => "apt-get update",
  require => Exec['add-php5-repo']
}

package { 'php5-cli':
  ensure => latest,
  require => Exec['apt-update']
}

package { 'php5-dev':
  ensure => installed,
  require => Package['php5-cli']
}

package { 'php-pear':
  ensure => installed,
  require => Package['php5-cli']
}

package { 'pkg-config':
  ensure => installed
}

package { 'libzmq-dev':
  ensure => installed
}

exec { 'zmq-beta':
  command => 'pecl install zmq-beta',
  unless => 'pecl list-all | grep Zero',
  require => [Package['pkg-config'], Package['libzmq-dev'], Package['php-pear']]
}

exec { 'zmq-extension':
  command => 'echo extension=zmq.so >> /etc/php5/cli/php.ini',
  unless => 'pecl list-all | grep Zero',
  require => Exec['zmq-beta']
}

exec { "composer-install":
  command => "/var/www/composer.phar install",
  unless => '[ -f /var/www/vendor/autoload.php ]',
  onlyif => 'pecl list-all | grep Zero',
  cwd => "/var/www/",
  require => Exec['zmq-extension']
}

exec { "create-named-pipe":
  command => "touch /usr/local/feed",
  unless => '[ -f /usr/local/feed ]'
}

exec { "start-exchange":
  command => "nohup php bin/exchange_loop.php &>/dev/null &",
  cwd => '/var/www',
  unless => 'ps -ef | grep "[0-9]\{2\} php bin/exchange_loop.php"',
  onlyif => 'pecl list-all | grep Zero',
  require => Exec['composer-install']
}

exec { "start-server":
  command => "nohup php -S 0.0.0.0:9999 -t bin &> /dev/null &",
  cwd => "/var/www",
  unless => 'ps -ef | grep "[0-9]\{2\} php -S 0.0.0.0:9999 -t bin/"',
  onlyif => 'pecl list-all | grep Zero',
  require => [Exec['start-exchange'], Exec['composer-install']]
}