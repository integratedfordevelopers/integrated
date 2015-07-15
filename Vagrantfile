# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure("2") do |config|

  config.vm.box = "debian-73-x64"
  config.vm.box_url = "http://puppet-vagrant-boxes.puppetlabs.com/debian-73-x64-virtualbox-puppet.box"

  # Create a forwarded port mapping which allows access to a specific port
  # within the machine from a port on the host machine. In the example below,
  # accessing "localhost:8080" will access port 80 on the guest machine.
  # we use port 8080 on localhost for MacOS support
  config.vm.network :forwarded_port, guest: 80, host: 8080
  #config.vm.network "private_network", type: "dhcp"

  # Share an additional folder to the guest VM. The first argument is
  # the path on the host to the actual folder. The second argument is
  # the path on the guest to mount the folder. And the optional third
  # argument is a set of non-required options.

  config.vm.synced_folder ".", "/vagrant", id: "share_id"

  # on a Mac you can use nfs for performance reasons
  #config.vm.synced_folder ".", "/vagrant", type: "nfs"

  # rsync is much better on Windows for performance reasons, although a sync
  # is not a very good option. This should be changed in the future when a better
  # solution becomes available
  # config.vm.synced_folder ".", "/vagrant", id: "share_id", type: "rsync", rsync__exclude: [
  #  ".git",
  #  ".idea",
  #  "app/bootstrap.php.cache",
  #  "app/cache/",
  #  "app/logs/",
  #  "build/",
  #  "web/assetic",
  #  "web/bundles/",
  #  "web/css/",
  #  "web/js/",
  #  "web/uploads",
  #  "web/cache"
  #]

  # folder for configuration files
  config.vm.synced_folder "conf.d", "/svr/conf.d"

  config.vm.provider :virtualbox do |virtualbox|
    virtualbox.customize ["modifyvm", :id, "--memory", 2048]
  end

  config.vm.provision :shell, :inline => "[ ! -e /svr/conf.d/.ssh/id_rsa ] || cp /svr/conf.d/.ssh/id_rsa /home/vagrant/.ssh/id_rsa"
  config.vm.provision :shell, :inline => "[ ! -e /svr/conf.d/.ssh/id_rsa.pub ] || cp /svr/conf.d/.ssh/id_rsa.pub /home/vagrant/.ssh/id_rsa.pub"

  config.vm.provision :shell, :inline => "aptitude -q2 update && aptitude -q2 install libaugeas-ruby augeas-tools -y"
  config.vm.provision :shell, :inline => "[ -d /etc/puppet/modules/apache ] || puppet module install puppetlabs/apache --version ~1.5"
  config.vm.provision :shell, :inline => "[ -d /etc/puppet/modules/apt ] || puppet module install puppetlabs/apt --version ~2.1"
  config.vm.provision :shell, :inline => "[ -d /etc/puppet/modules/mongodb ] || puppet module install puppetlabs/mongodb --version ~0.11"
  config.vm.provision :shell, :inline => "[ -d /etc/puppet/modules/nodejs ] || puppet module install puppetlabs/nodejs --version ~0.7"
  config.vm.provision :shell, :inline => "[ -d /etc/puppet/modules/mysql ] || puppet module install puppetlabs/mysql --version ~3.4"

  config.vm.provision :puppet do |puppet|
    puppet.manifests_path = "vagrant/puppet/manifests"
    puppet.manifest_file  = "site.pp"
  end
end
