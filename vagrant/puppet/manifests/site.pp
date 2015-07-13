# defaults

Exec {
	path => '/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin', # echo $PATH
}

Exec["apt_update"] -> Package <| |>

class { 'apt':
	purge_sources_list   => true,
	purge_sources_list_d => true,
	purge_preferences_d  => true
}

apt::source { 'debian_stable':
	location    => 'http://ftp.nl.debian.org/debian/',
	release     => 'wheezy',
	repos       => 'main contrib non-free',
	include_src => true
}

apt::source { 'debian_stable-security':
	location    => 'http://security.debian.org/',
	release     => 'wheezy/updates',
	repos       => 'main contrib non-free',
	include_src => true
}

apt::source { 'debian_stable-updates':
	location    => 'http://ftp.nl.debian.org/debian/',
	release     => 'wheezy-updates',
	repos       => 'main contrib non-free',
	include_src => true
}

apt::source { 'debian_stable-backports':
	location    => 'http://ftp.nl.debian.org/debian/',
	release     => 'wheezy-backports',
	repos       => 'main',
	pin         => 200,
	include_src => true
}

# git

package { [git]:
	ensure  => latest,
}

# mount some tmpfs drives

#mount { '/vagrant/app/cache/':
#	ensure   => mounted,
#	device   => 'tmpfs',
#	fstype   => 'tmpfs',
#	options  => "noauto,size=128m,uid=1000,gid=1000,mode=777",
#	atboot	 => false,
#	remounts => false,
#}
#
#mount { '/vagrant/app/logs/':
#	ensure   => mounted,
#	device   => 'tmpfs',
#	fstype   => 'tmpfs',
#	options  => "noauto,size=32m,uid=1000,gid=1000,mode=777",
#	atboot	 => false,
#	remounts => false,
#}

# setup apache and php

class {'apache':
	default_vhost => false,
	mpm_module    => 'prefork',
	servername    => 'development',
	user          => 'vagrant'
}

class {'apache::mod::php': }
class {'apache::mod::rewrite': }

package { [php-apc, php5-intl, php5-curl, php5-xdebug]:
	ensure  => latest,
	require => Class['apache::mod::php'],
	notify  => Service['httpd'],
}

package { [php5-cli]:
	ensure  => latest,
}

package { [phpmyadmin, javascript-common]:
	ensure  => present,
	require => Class['mysql::bindings'],
	notify  => Service['httpd'],
}

file { '/etc/apache2/conf.d/phpmyadmin.conf':
	ensure => 'link',
	target  => "/etc/phpmyadmin/apache.conf",
	require => Package['phpmyadmin'],
	notify  => Service['httpd'],
}

file { '/etc/apache2/conf.d/javascript-common.conf':
	ensure => 'link',
	target  => "/etc/javascript-common/javascript-common.conf",
	require => Package['javascript-common'],
	notify  => Service['httpd'],
}

augeas { '/etc/php5/apache2/php.ini':
	context => '/files/etc/php5/apache2/php.ini',
	changes => [
		'set PHP/error_reporting "E_ALL | E_STRICT"',
		'set PHP/max_execution_time 60',
		'set PHP/memory_limit 512M',
		'set PHP/display_errors On',
		'set PHP/display_startup_errors On',
		'set PHP/html_errors On',
		'set PHP/short_open_tag Off',
		'set PHP/post_max_size 100M',
		'set PHP/upload_max_filesize 100M',
		'set Date/date.timezone Europe/Amsterdam',
	],
	require => Class['apache::mod::php'],
	notify  => Service['httpd'],
}

augeas { '/etc/php5/conf.d/xdebug.ini':
	context => '/files/etc/php5/conf.d/xdebug.ini',
	changes => [
		'set Xdebug/xdebug.max_nesting_level 250',
		'set Xdebug/xdebug.var_display_max_depth 8',
    'set Xdebug/xdebug.remote_enable 1',
    'set Xdebug/xdebug.remote_handler dbgp',
    'set Xdebug/xdebug.remote_host 10.0.2.2',
    'set Xdebug/xdebug.remote_port 9000',
    'set Xdebug/xdebug.remote_autostart 0',
    'set Xdebug/xdebug.profiler_enable_trigger 1',
    'set Xdebug/xdebug.profiler_output_dir /vagrant'
	],
	require => Package['php5-xdebug'],
	notify  => Service['httpd'],
}

apache::vhost { 'symfony':
	port               => '80',
	docroot            => '/vagrant/web/',
	docroot_owner      => 'vagrant',
	docroot_group      => 'vagrant',
	override           => 'all',
}

# setup mongodb server

class { 'mongodb::globals': manage_package_repo => true }
class { 'mongodb::server': }

package { ["php5-mongo"]:
	ensure  => latest,
	require => Class['apache::mod::php'],
	notify  => Service['httpd'],
}

file { '/etc/php5/conf.d/20-mongo.ini':
	ensure  => link,
	target  => "../mods-available/mongo.ini",
	require => Package['php5-mongo'],
	notify  => Service['httpd']
}


# setup mysql server

class { 'mysql::server': root_password => 'root' }

class { 'mysql::bindings':
	php_enable => true,
	require => Class['apache::mod::php']
}

mysql::db { 'integrated':
	user     => 'integrated',
	password => 'integrated',
	host     => 'localhost',
	grant    => ['all'],
}

# Install nodejs and less

class { 'nodejs':
    manage_repo => true
}

exec {'create node symlink':
    command => 'ln -s /usr/bin/nodejs /usr/bin/node',
    before => Exec['npm install lessc'],
    unless => "[ -L /usr/bin/node ]",
    require => Package['npm']
}

exec {'npm install lessc':
    command => 'npm install -g less@1.7.5',
    require => Package['npm'],
}

exec {'npm install bower':
    command => 'npm install -g bower',
    require => Package['npm'],
}