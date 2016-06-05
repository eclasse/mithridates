# mithridates
Webb-template Mithridates based on webb-template Anax.


# Usage
Mithridates is an easy to use webb-template for smaller websites.

The src-folder contains a few classes for handling Database-calls, Image-manipulation and Content/Blog-management, among others. A bootstrap-script autoloads these class when new objects of them are declared.

The webroot-folder is where the content goes. An index-page serves as the default template. By adding new pages you can create your own content. The config-file ties the pieces together. In it you can configure database-information, add stylesheets and menu-items. The img-file is a script working together with the CImage-class in the src-folder. The webroot could also house two folders; img and css, where you put your images and css-files respectively.

The theme-folder containins functions for handling the theme of the website. A default template index.tpl.php is provided.

# Classes

CDatabase: a class which contains functions for connecting, writing and reading to a database via PDO. As mentioned, connection requires database-information to be provided in the config-file in the webroot-folder.

CImage: a class which contains numerous functions for processing and manipulating images via the img.php in the webroot-folder.

CTextFilter: a class which provides the possibility of adding various textfilters to strings. 

