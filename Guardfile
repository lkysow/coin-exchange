# Guardfile for Symfony2. Place in the top level of your Symfony2 directory.

notification :growl
guard 'phpunit', :tests_path => 'src'  do
  watch(%r{^.+Test\.php$}) # Watch all your tests
  watch(%r{(.+/.+)/(.+)\.php$})	{ |m| "#{m[1]}/Tests/#{m[2]}Test.php" } # Watch all files in your bundles and run the respective tests on change
end
