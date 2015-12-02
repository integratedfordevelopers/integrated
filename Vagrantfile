# -*- mode: ruby -*-

dir = File.dirname(File.expand_path(__FILE__))

vendordir = "#{dir}/vendor/integrated"

require 'yaml'
require "#{vendordir}/puphpet/ruby/deep_merge.rb"

# include default config provided by package
configValues = YAML.load_file("#{vendordir}/puphpet/config.yaml")

# include config in project directory (when available)
if File.file?("#{dir}/puphpet/config-custom.yaml")
  custom = YAML.load_file("#{dir}/puphpet/config-custom.yaml")
  configValues.deep_merge!(custom)
end

data = configValues['vagrantfile']

Vagrant.require_version '>= 1.6.0'

eval File.read("#{vendordir}/puphpet/vagrant/Vagrantfile-#{data['target']}")
