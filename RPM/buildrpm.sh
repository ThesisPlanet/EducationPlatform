#!/bin/sh
## $1 = workspace
## $2 = build_version
mkdir build-$2
cp -r src docs etc build-$2
/bin/tar czf /var/lib/jenkins/rpmbuild/SOURCES/$2.tar.gz -C $1 build-$2
rm -rf build-$2 src docs etc
eval $(gpg-agent --daemon --use-standard-socket)
/usr/bin/rpmbuild --define "BUILD_NUMBER $2" -ba $1/RPM/dep.spec
chmod +x $1/RPM/rpm-sign.exp
$1/RPM/rpm-sign.exp /var/lib/jenkins/rpmbuild/RPMS/noarch/TP-DEP-$2-1.noarch.rpm
/bin/rm ~/rpmbuild/SOURCES/$2.tar.gz

## refresh the RPM repo.
/usr/bin/createrepo /var/lib/jenkins/rpmbuild/RPMS/noarch/
## upload the RPM repo to Web server.
scp -r /var/lib/jenkins/rpmbuild/RPMS/noarch/* web@10.1.6.1:/var/www/TP-RPM