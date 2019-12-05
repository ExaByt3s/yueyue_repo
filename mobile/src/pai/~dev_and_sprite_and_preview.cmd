@echo off

cd.>public_tag.txt
echo is_predev>>public_tag.txt
pure release -wpDd ../../m2predev

:end