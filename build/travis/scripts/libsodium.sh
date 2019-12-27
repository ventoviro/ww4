#!/bin/sh

git clone -b stable https://github.com/jedisct1/libsodium.git
cd libsodium && sudo ./configure && sudo make check && sudo make install && cd ..
