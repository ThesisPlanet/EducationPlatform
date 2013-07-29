Name: TP-DEP
Summary: A php based corporate training platform
Version: %{?BUILD_NUMBER}
Release: 1
License: BSD3 -- see copyright.md (Copyright 2013, Thesis Planet, LLC. All Rights Reserved.)
Group: Development/Libraries
Source0: http://www.thesisplanet.com/dep/releases/%{version}.tar.gz
BuildRoot: %{_tmppath}/%{name}-root
BuildArch: noarch
Url: http://www.thesisplanet.com

Requires: php >= 5.3.0, mysql-server, nginx, supervisor, gearmand, php-pecl-gearman

%description
A corporate training platform capable of sharing Audio, Files, and Videos.
Ability to provide roles and permissions to content

%prep
%setup -q -n build-%{?BUILD_NUMBER}
%build

%install
rm -rf $RPM_BUILD_ROOT
mkdir -p $RPM_BUILD_ROOT%{_datadir}/%{name}/{src,configuration,shared}
mkdir -p $RPM_BUILD_ROOT/etc/supervisord.d
cp -r ./src/* $RPM_BUILD_ROOT%{_datadir}/%{name}/src
cp ./etc/supervisord.d/ThesisPlanet.ini $RPM_BUILD_ROOT/etc/supervisord.d/ThesisPlanet.ini
%clean
rm -rf  $RPM_BUILD_ROOT
%files
%defattr(0600,web,web)
%{_datadir}/%{name}/*
/etc/supervisord.d/ThesisPlanet.ini
%post
## backup database
##APPLICATION_ENV=production php %{_datadir}/%{name}/src/bin/App/createBackup.php
## update database if necessary
APPLICATION_ENV=production php %{_datadir}/%{name}/src/bin/doctrine.php orm:schema-tool:update --force
APPLICATION_ENV=production php %{_datadir}/%{name}/src/bin/App/checkForAdmin.php
## restart nginx
/etc/init.d/nginx restart
/etc/init.d/php-fpm restart
/etc/init.d/supervisord restart

## restart supervisord