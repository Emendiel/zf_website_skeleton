namespace :js do
  require 'yaml'
  
  conf = YAML.load_file('application/configs/jsconfig.yaml')
  path_js_src   = 'public/js/src/'
  path_js_build = 'public/js/build/'
  
  desc "Default task: clean, combine and minify js"
  task :default => [:clean, :combine, :minify] do
  end
  
  desc "combine " + path_js_src + " into " + path_js_build + " with packages defined in jsconfig.yaml"
  task :combine do  
    puts "= combine =" 
    
    conf.each do |packageName, package|
      puts "combine package " + packageName
      
      mergedString = ""
      
      package.each do |file|
        puts "- reading " + file.to_s + ".js"
        
        path = path_js_src + file + ".js"
        
        if File.exists?(path)
          File.open(path,'r'){ |f| mergedString += f.read + "\n"}
        else
          throw "ERROR: file " + path + " doesn't exist"
        end
      end
      
      puts "-> creating combined file " + packageName + '.js'
      
      File.open(path_js_build + packageName + ".js", "w") { |f| f.write(mergedString)}
    end
  end
  
  desc "Minify all *.js in " + path_js_build
  task :minify do
    puts "= minify js ="
    
    FileList.new(path_js_build + '*.js').exclude(path_js_build + '*.min.js').each do |file, value|
      puts "-> minifing " + file.to_s + " into " + path_js_build + File.basename(file, ".js") + ".min.js"
      sh("java -jar tools/yuicompressor-2.4.4.jar --type js --charset utf8 -o " + path_js_build + File.basename(file, ".js") + ".min.js", file) do |ok, res|
        if ! ok
          throw "ERROR: status = #{res.exitstatus}"
        end
      end
    end
  end
  
  desc "drop all files in js/ build"
  task :clean do
    puts "= clean build ="
    
    FileList.new(path_js_build + '*.js').each do |file, value|
      puts "x deleting " + file.to_s
      File.unlink(file)
    end
  end
end