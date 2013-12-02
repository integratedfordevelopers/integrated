# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure("2") do |config|

  config.vm.box = "debian-70rc1-x64"
  config.vm.box_url = "http://puppet-vagrant-boxes.puppetlabs.com/debian-70rc1-x64-vbox4210.box"

  # Create a forwarded port mapping which allows access to a specific port
  # within the machine from a port on the host machine. In the example below,
  # accessing "localhost:8080" will access port 80 on the guest machine.
  # config.vm.network :forwarded_port, guest: 80, host: 8080
  config.vm.network :forwarded_port, guest: 80, host: 80

  # Create a private network, which allows host-only access to the machine
  # using a specific IP.
  # config.vm.network :private_network, ip: "192.168.33.10"

  # Create a public network, which generally matched to bridged network.
  # Bridged networks make the machine appear as another physical device on
  # your network.
  # config.vm.network :public_network

  # Share an additional folder to the guest VM. The first argument is
  # the path on the host to the actual folder. The second argument is
  # the path on the guest to mount the folder. And the optional third
  # argument is a set of non-required options.
  config.vm.synced_folder ".", "/vagrant", :nfs => false
  config.vm.synced_folder "conf.d", "/svr/conf.d"

  config.vm.provider :virtualbox do |virtualbox|
    virtualbox.customize ["modifyvm", :id, "--memory", 1024]
  end

  config.vm.provision :shell, :inline => "cp /svr/conf.d/.ssh/id_rsa /home/vagrant/.ssh/id_rsa"
  config.vm.provision :shell, :inline => "cp /svr/conf.d/.ssh/id_rsa.pub /home/vagrant/.ssh/id_rsa.pub"

  config.vm.provision :shell, :inline => "aptitude -q2 update && aptitude -q2 install libaugeas-ruby augeas-tools -y"
  config.vm.provision :shell, :inline => "[ -d /etc/puppet/modules/apache ] || puppet module install puppetlabs/apache"
  config.vm.provision :shell, :inline => "[ -d /etc/puppet/modules/apt ] || puppet module install puppetlabs/apt"
  config.vm.provision :shell, :inline => "[ -d /etc/puppet/modules/mongodb ] || puppet module install puppetlabs/mongodb"
  config.vm.provision :shell, :inline => "[ -d /etc/puppet/modules/nodejs ] || puppet module install puppetlabs/nodejs"
#  config.vm.provision :shell, :inline => "[ -d /etc/puppet/modules/mysql ] || puppet module install puppetlabs/mysql"

  config.vm.provision :puppet do |puppet|
    puppet.manifests_path = "vagrant/puppet/manifests"
    puppet.manifest_file  = "site.pp"
#	puppet.options        = [
#		'--verbose',
#		'--debug',
#	]
  end
end
