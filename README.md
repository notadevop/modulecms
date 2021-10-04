https://img.shields.io/badge/php-7.4.1-green

# modulecms (Модульная CMS)
###### Домащняя CMS не претендует на какой проект, 
###### создана для понимания языка программирования PHP и принципов его работы 
#
###### Идея в том, чтобы конвертировать CMS в любой удобный формат

- для правильной работы в .htaccess нужно прописать 

```
Options -MultiViews
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^ index.php [QSA,L]
```

