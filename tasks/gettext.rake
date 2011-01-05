namespace :gettext do

  desc "Load gettext"
  task :encode, :files do |cmd, args|
    begin
      
      aFiles = args[:files].split(",")
      
      Dir.glob("#{ROOT}/data/locales/*").each do |locale|
        aFiles.each do |file|
          if File.exist?("#{locale}/#{file}.po")
            sh "msgfmt -v -o #{locale}/#{file}.mo -D #{locale} #{file}.po"
          else
            puts "Warning : #{locale}/#{file}.po doesn't exist."
          end
        end
      end
    rescue => e
      puts "error : #{e.inspect}"
    end
  end
end