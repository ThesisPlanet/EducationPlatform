#!/bin/sh
echo "This will create folders and set permissions as necessary."
if [ -d "../untrusted" ]; 
then
echo "untrusted already exists."
else
mkdir ../untrusted
mkdir ../untrusted/CFD
mkdir ../untrusted/CFS
mkdir ../untrusted/file
mkdir ../untrusted/audio
mkdir ../untrusted/video
chmod -R 0777 ../untrusted
fi
echo "Created ../untrusted/* directories."

echo "Checking to see if entity proxy directory exists"
if [ -d "../library/App/Entity/Proxy" ]; 
then
echo "Proxy directory already exists. Assuming appropriate permissions"
else
mkdir ../library/App/Entity/Proxy
chmod -R 0777 ../library/App/Entity/Proxy
echo "App/Entity/Proxy created."
fi
echo "Directories have been initialized. Have a nice day!"
