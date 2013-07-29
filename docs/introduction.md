Welcome to the Thesis Planet introductory documentation.

There are a few key concepts and technologies worth noting for new developers.

First of all, this application is currently built using Zend Framework 1.x
Doctrine is used to relate the database and PHP object (entities).

Changes to entities that would result in a schema change in the database will require the use of src/bin/doctrine.php.

Typical commands for Schema changes:
1. Create the SQL that will update the database. Use this for reviewing any changes that will be made.
	php src/bin/doctrine.php orm:schema-tool:update --dump-sql
2. Once you are sure that the changes look good, go ahead and run the force command.
	php src/bin/doctrine.php orm:schema-tool:update --force

	

Regarding unit testing:
	All of the service should be unit tested. Controller testing is not as much of a priority.

Regarding deployment:
	Jenkins CI is used for continuous deployment. Jenkins then interfaces with capistrano and pushes code to the servers according to the Capfile and config/deploy.rb files.

Regarding server configuration management:
	Puppet is used to maintain consistent operating system configurations. 
	In addition package management and configuration files are managed by puppet. This includes how PHP is deployed.
	
The ultimate goal is to simplify how development is done by increasing the levels of automation.


Folder structure:
src/library/App - Core application logic goes here.
src/library/Acl - Definitions for general access control.
src/library/Auth - A custom Authentication adapater that helps with subscription management based on the incoming url.
src/library/ContentAcl - A Dynamic ACL generator per content object. This validates that a user can access a given piece of content.
src/library/App/Entity - These represent the objects that are interacted with.
src/library/App/Form - These are the forms that are used to ensure that valid information is being provided to the services. These should reflect the expected formats that will be handled by the entities.
src/library/App/Proxy - This is a folder for handling the Proxy entity objects created by Doctrine.
src/library/App/Repository - These are the repositories. They are where complex DQL/SQL queries are put. Service layers interact with repositories. 
src/library/App/Service - Business logic goes here. Think of these as an internal API that is exposed to controllers, web-facing APIs, or command line input.
src/library/App/Validate - Some custom validators like checking for username availability.
src/library/Aws - Amazon's AWS SDK for PHP
src/library/Bisna - the Bisna glue for Doctrine and ZF
src/library/Fonts - Fonts used for captcha's if needed.
src/library/Gearman - Critical for application task processing.
src/library/Gearman/DEP - Digital Education Platform specific tasks.
src/library/Gearman/Worker.php - Executed on worker machines. This should be managed by supervisorD and restarted whenever new code is deployed provided that no tasks are running.
src/library/pChart - pChart library for creating reports and such.
src/library/vendor - External libraries
src/library/TP - Thesis Planet shared-type of logic goes here.


Front-End stuff:

src/public/index.php (All traffic is routed to here if it 404's by default -- NGINX tries it first).

	Libraries:
		css/libs/libName/version/libname.{min.}css
		img/libs/libName/version/libname.{min.}[gif|png|jpg]	
		js/libs/libName/version/libname.{min.}js
	JQueryUI themes:
		/css/libs/JQueryUI/themes/themename/*.*
