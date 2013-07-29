### DEPLOY TO TESTING SERVER ###




set :application, "dep.thesisplanet.com"
set :scm, "git"
set :repository, "file://."
set :branch, "HEAD"
set :deploy_via, :copy

set :user, "web"

set :deploy_to, "/var/www/dep.thesisplanet.com"
set :current_path, "#{deploy_to}/current"
set :releases_path, "#{deploy_to}/releases/"
set :shared_path, "#{deploy_to}/shared"
set :app_config_path, "#{deploy_to}/configuration"
set :use_sudo, false

role :web, "10.1.5.1"                          # Your HTTP server, Apache/etc
role :app, "10.1.5.1"                          # This may be the same as your `Web` server
role :db,  "10.1.5.1", :primary => true # This is where Rails migrations will run
#role :db,  "your slave db-server here"

after "deploy:restart", "deploy:cleanup"
after "deploy", "deploy:update_db"
after "deploy", "deploy:restart_services"

namespace :deploy do
	task :update_db, :roles => :app do
    	run "cd #{deploy_to}/current && sh #{deploy_to}/current/updateDBProduction.sh"
	end
	task :restart_services, :roles => :app do
		run "sudo /etc/init.d/php-fpm restart && sudo /etc/init.d/nginx restart && sudo /etc/init.d/supervisord restart"
	end
end