namespace :db do
  require 'yaml'

  desc "load seed"
  task :seed do
  end
  
  desc "migrate database"
  task :migrate do
    conf = YAML.load_file('application/configs/application.yaml')
    conf.each do |key|
      puts key
    end

    sh'liquibase --driver=com.mysql.jdbc.Driver --username=<username> --password=<password> --url=jdbc:mysql://<db_host>/<bm_db_name> --changeLogFile=schema.xml --logLevel=debug update' rescue puts 'liquibase >=2.0.1 must be installed'
  end

  desc "rollback database"
  task :rollback do
  end

  desc "drop all tables in database"
  task :drop do
  end
end