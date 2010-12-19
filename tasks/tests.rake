namespace :tests do
  desc "Install PhpUnit"
  task :install do
    sh "pear channel-discover pear.phpunit.de" rescue puts "PEAR must be installed"
    sh "pear channel-discover components.ez.no"
    sh "pear channel-discover pear.symfony-project.com"
    sh "pear install --allDeps phpunit/PHPUnit"
  end
  
  desc "Run All Test Suite"
  task :run do
    sh "phpunit --configuration tests/phpunit.xml" rescue puts "PEAR::PhpUnit must be installed"
  end
end