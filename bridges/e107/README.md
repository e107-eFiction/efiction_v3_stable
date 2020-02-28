This folder contains the default files for connecting to the default author
table for eFiction.  

They are included here as a backup in case you need 
to restore from a failed bridge.

This bridge can be used only for efiction installed as the subdirectory of e107 site

1. add 
`@ include_once(UN_FULLAPP_PATH."class2.php");`

after 
`@ include_once(_BASEDIR."config.php");`

- you can't add this directly in config.php, because than ajax will stop to work

`UN_FULLAPP_PATH` is constant defined in config.php - path to your e107 installation .  

2.  replace files from this directory

queries.php -  allows to use e107 user table as authors

get_session_vars.php - allows to use e107 constants to detect if user is logged or not.  Thanks installation in subdirectory, all cookies/sessions are available for efiction site too.

using class2.php allows use any e107 feature inside efiction script

3. change paths in panels for login, signup and user info to e107 urls

if your e107 use the same theme as efiction, user will not notice difference







