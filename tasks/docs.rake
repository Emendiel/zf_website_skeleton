namespace :docs do

  desc "Install PhpDocumentor"
  task :install do
    sh "pear install PhpDocumentor" rescue puts "PEAR must be installed"
  end
  
  desc "Generate Documentation"
  task :generate do
    sh "phpdoc -d #{ROOT}/application -o HTML:frames -t #{ROOT}/docs/api -ti Mamba_Nation_Website" rescue puts "PEAR::PhpDocumentator must be installed"
  end
end
