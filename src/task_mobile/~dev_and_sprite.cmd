@echo off

cd.>public_tag.txt
echo is_dev>>public_tag.txt
pure release -wLpDd ../../task/mdev

:end