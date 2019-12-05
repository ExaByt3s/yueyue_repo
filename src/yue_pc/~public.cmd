@echo off

cd.>public_tag.txt
echo is_pub>>public_tag.txt
pure release -wopmDd ../../pc

:end