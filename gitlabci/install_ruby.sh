#!/bin/bash

# Update package list
apt-get update -yqq

# Install Ruby-dev
apt-get install ruby-dev -yqq
apt-get install libgemplugin-ruby -yqq