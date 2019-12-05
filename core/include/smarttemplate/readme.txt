2005-05-11 add cache_ignore_str to ignore save cache when such error str. Tony

2005-05-10 fix cache dir bug by Tony


SmartTemplate 1.0
Author: Philipp von Criegern (philipp@criegern.com)
Release Date: 22.08.2002
License: GPL

/*
class.smarttemplate.php ÐÞÕýÁËglobal bug (CSDN EXPERTS)
*/

How To Install:
1. Download and unzip the smarttemplate package. The following files should be unpacked:

   class.smarttemplate.php
   class.smarttemplatedebugger.php
   class.smarttemplateparser.php
   readme.txt
   examples/Alternating_Colors.html
   examples/Alternating_Colors.php
   examples/DropDown_Boxes.html
   examples/DropDown_Boxes.php
   examples/Hello_World.html
   examples/Hello_World.php
   examples/Iterating_Blocks.html
   examples/Iterating_Blocks.php
   examples/Nested_Blocks.html
   examples/Nested_Blocks.php
   examples/Special_Characters.html
   examples/Special_Characters.php
   smarttemplate_compiled/
   smarttemplate_extensions/smarttemplate_extension_config.php
   smarttemplate_extensions/smarttemplate_extension_current_date.php
   smarttemplate_extensions/smarttemplate_extension_current_datetime.php
   smarttemplate_extensions/smarttemplate_extension_current_time.php
   smarttemplate_extensions/smarttemplate_extension_dateformat.php
   smarttemplate_extensions/smarttemplate_extension_dateformatgrid.php
   smarttemplate_extensions/smarttemplate_extension_db_date.php
   smarttemplate_extensions/smarttemplate_extension_db_datetime.php
   smarttemplate_extensions/smarttemplate_extension_db_time.php
   smarttemplate_extensions/smarttemplate_extension_encode.php
   smarttemplate_extensions/smarttemplate_extension_entity_decode.php
   smarttemplate_extensions/smarttemplate_extension_gettext.php
   smarttemplate_extensions/smarttemplate_extension_header.php
   smarttemplate_extensions/smarttemplate_extension_help.php
   smarttemplate_extensions/smarttemplate_extension_hidemail.php
   smarttemplate_extensions/smarttemplate_extension_htmlentities.php
   smarttemplate_extensions/smarttemplate_extension_load_config.php
   smarttemplate_extensions/smarttemplate_extension_load_file.php
   smarttemplate_extensions/smarttemplate_extension_lowercase.php
   smarttemplate_extensions/smarttemplate_extension_mailto.php
   smarttemplate_extensions/smarttemplate_extension_number.php
   smarttemplate_extensions/smarttemplate_extension_nvl.php
   smarttemplate_extensions/smarttemplate_extension_options.php
   smarttemplate_extensions/smarttemplate_extension_regex.php
   smarttemplate_extensions/smarttemplate_extension_replace.php
   smarttemplate_extensions/smarttemplate_extension_select_site.php
   smarttemplate_extensions/smarttemplate_extension_session.php
   smarttemplate_extensions/smarttemplate_extension_stringformat.php
   smarttemplate_extensions/smarttemplate_extension_substr.php
   smarttemplate_extensions/smarttemplate_extension_textbutton.php
   smarttemplate_extensions/smarttemplate_extension_trim.php
   smarttemplate_extensions/smarttemplate_extension_truncate.php
   smarttemplate_extensions/smarttemplate_extension_uppercase.php
   smarttemplate_extensions/smarttemplate_extension_urlencode.php
   smarttemplate_extensions/smarttemplate_extension_vardump.php

2. Copy the class.*.php files and the smarttemplate_extensions folder to your PHP include folder
   (Specified in your php.ini: include_path= XXX)

3. Edit the class.smarttemplate.php file and adjust the configuration according to your system configuration:
   $temp_dir  : The folder where the compiled templates can be stored. PHP must have write access to this folder!
   $cache_dir : The folder where output cache files can be stored (if used). PHP must have write access to this folder!

4. Copy the examples folder to somewhere below your document root, so you can access with your browser.

5. Have a look at the examples in the examples folder to see how SmartTemplate works.
