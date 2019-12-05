@echo off

cd.>public_tag.txt
echo is_dev>>public_tag.txt
pure release -wLpDd ../../user/local_pc

:end