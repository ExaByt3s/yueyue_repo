@echo off

cd.>public_tag.txt
echo is_predev>>public_tag.txt
pure release -wpDd ../../sell_adminpredev

:end