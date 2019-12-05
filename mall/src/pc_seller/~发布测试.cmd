@echo off

cd.>public_tag.txt
echo is_pub_test>>public_tag.txt
pure release -wopmDd ../../seller/local_pub

:end