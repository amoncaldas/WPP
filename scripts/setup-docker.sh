#!/bin/bash

# function to install docker
installDocker() {
  sudo su
  wget -qO- https://get.docker.com/ | sh
  exit
  sudo usermod -a -G docker $USER
  su - $USER

  docker run hello-world

  sudo su
  curl -L https://github.com/docker/compose/releases/download/1.6.2/docker-compose\
  -`uname -s`-`uname -m` > /usr/local/bin/docker-compose
  chmod +x /usr/local/bin/docker-compose

  docker-compose version

  curl -L https://github.com/docker/machine/releases/download/v0.6.0/docker-machine\
  -`uname -s`-`uname -m` > /usr/local/bin/docker-machine && \
  chmod +x /usr/local/bin/docker-machine

  docker-machine version
}

## Check if docker is installed. If not, install it
which docker

if [ $? -eq 0 ]
then
    docker --version | grep "Docker version"
    if [ $? -eq 0 ]
    then
        echo "Docker installed"
    else
        echo "Docker not installed, installing it..."
        installDocker
    fi
else
    echo "Docker not installed, installing..."
    installDocker
fi

