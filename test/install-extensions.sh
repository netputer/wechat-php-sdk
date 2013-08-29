git clone --depth=1 --branch=master git://github.com/php-test-helpers/php-test-helpers.git php-test-helpers
cd php-test-helpers
phpize
./configure --enable-test-helpers
make
res=`sudo make install`
echo "zend_extension =${res##*Installing shared extensions:    }test_helpers.so"
echo "zend_extension =${res##*Installing shared extensions:    }test_helpers.so" >> ~/.phpenv/versions/$(phpenv version-name)/etc/conf.d/xdebug.ini
cd ..
php --version
