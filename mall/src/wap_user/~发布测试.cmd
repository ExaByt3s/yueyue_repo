@echo off

cd.>public_tag.txt
echo is_pub_test>>public_tag.txt
pure release -wopmDd ../../user/local_pub

:end