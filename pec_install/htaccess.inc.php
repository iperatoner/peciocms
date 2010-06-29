<?php

return
'RewriteEngine On

# Home
RewriteRule ^home\/?$																{%PECIO_PATH%}index.php?target=home [L]

# Article
RewriteRule ^article/([\w\d-]+)\/?$													{%PECIO_PATH%}index.php?target=article&id=$1 [L]

# Search
RewriteRule ^search\/$																{%PECIO_PATH%}index.php?target=search [QSA,L]


# Blog
RewriteRule ^blog\/?$																{%PECIO_PATH%}index.php?target=blog [L]

# Blog Page
RewriteRule ^(blog|home)/p/([\d]+)\/?$														{%PECIO_PATH%}index.php?target=$1&p=$2 [L]


# Post New Comment
RewriteRule ^(blog|home)/post/([\w\d-]+)/(new-comment)\/?$									{%PECIO_PATH%}index.php?target=$1&post_id=$2&action=new-comment [L]

# Post
RewriteRule ^(blog|home)/post/([\w\d-]+)\/?$												{%PECIO_PATH%}index.php?target=$1&post_id=$2 [QSA,L]


# Blog Category Page
RewriteRule ^(blog|home)/category/([\w\d-]+)/p/([\d]+)\/?$									{%PECIO_PATH%}index.php?target=$1&category=$2&p=$3 [L]

# Blog Category
RewriteRule ^(blog|home)/category/([\w\d-]+)\/?$											{%PECIO_PATH%}index.php?target=$1&category=$2 [L]


# Blog Tag Page
RewriteRule ^(blog|home)/tag/([\w\d-]+)/p/([\d]+)\/?$										{%PECIO_PATH%}index.php?target=$1&tag=$2&p=$3 [L]

# Blog Tag
RewriteRule ^(blog|home)/tag/([\w\d-]+)\/?$												{%PECIO_PATH%}index.php?target=$1&tag=$2 [L]


# Archive Page
RewriteRule ^(blog|home)/archive/d-([\d]{2})/m-([\d]{2})/y-([\d]{4})/p/([\d]+)\/?$			{%PECIO_PATH%}index.php?target=$1&day=$2&month=$3&year=$4&p=$5 [L]
RewriteRule ^(blog|home)/archive/d-([\d]{2})/y-([\d]{4})/m-([\d]{2})/p/([\d]+)\/?$			{%PECIO_PATH%}index.php?target=$1&day=$2&year=$3&month=$4&p=$5 [L]
RewriteRule ^(blog|home)/archive/m-([\d]{2})/d-([\d]{2})/y-([\d]{4})/p/([\d]+)\/?$			{%PECIO_PATH%}index.php?target=$1&month=$2&day=$3&year=$4&p=$5 [L]
RewriteRule ^(blog|home)/archive/m-([\d]{2})/y-([\d]{4})/d-([\d]{2})/p/([\d]+)\/?$			{%PECIO_PATH%}index.php?target=$1&month=$2&year=$3&day=$4&p=$5 [L]
RewriteRule ^(blog|home)/archive/y-([\d]{4})/d-([\d]{2})/m-([\d]{2})/p/([\d]+)\/?$			{%PECIO_PATH%}index.php?target=$1&year=$2&day=$3&month=$4&p=$5 [L]
RewriteRule ^(blog|home)/archive/y-([\d]{4})/m-([\d]{2})/d-([\d]{2})/p/([\d]+)\/?$			{%PECIO_PATH%}index.php?target=$1&year=$2&month=$3&day=$4&p=$5 [L]

RewriteRule ^(blog|home)/archive/d-([\d]{2})/m-([\d]{2})/p/([\d]+)\/?$						{%PECIO_PATH%}index.php?target=$1&day=$2&month=$3&p=$4 [L]
RewriteRule ^(blog|home)/archive/m-([\d]{2})/d-([\d]{2})/p/([\d]+)\/?$						{%PECIO_PATH%}index.php?target=$1&month=$2&day=$3&p=$4 [L]
RewriteRule ^(blog|home)/archive/m-([\d]{2})/y-([\d]{4})/p/([\d]+)\/?$						{%PECIO_PATH%}index.php?target=$1&month=$2&year=$3&p=$4 [L]
RewriteRule ^(blog|home)/archive/y-([\d]{4})/m-([\d]{2})/p/([\d]+)\/?$						{%PECIO_PATH%}index.php?target=$1&year=$2&month=$3&p=$4 [L]
RewriteRule ^(blog|home)/archive/d-([\d]{2})/y-([\d]{4})/p/([\d]+)\/?$						{%PECIO_PATH%}index.php?target=$1&day=$2&year=$3&p=$4 [L]
RewriteRule ^(blog|home)/archive/y-([\d]{4})/d-([\d]{2})/p/([\d]+)\/?$						{%PECIO_PATH%}index.php?target=$1&year=$2&day=$3&p=$4 [L]

RewriteRule ^(blog|home)/archive/d-([\d]{2})/p/([\d]+)\/?$									{%PECIO_PATH%}index.php?target=$1&day=$2&p=$3 [L]
RewriteRule ^(blog|home)/archive/m-([\d]{2})/p/([\d]+)\/?$									{%PECIO_PATH%}index.php?target=$1&month=$2&p=$3 [L]
RewriteRule ^(blog|home)/archive/y-([\d]{4})/p/([\d]+)\/?$									{%PECIO_PATH%}index.php?target=$1&year=$2&p=$3 [L]

# Archive
RewriteRule ^(blog|home)/archive/d-([\d]{2})/m-([\d]{2})/y-([\d]{4})\/?$					{%PECIO_PATH%}index.php?target=$1&day=$2&month=$3&year=$4 [L]
RewriteRule ^(blog|home)/archive/d-([\d]{2})/y-([\d]{4})/m-([\d]{2})\/?$					{%PECIO_PATH%}index.php?target=$1&day=$2&year=$3&month=$4 [L]
RewriteRule ^(blog|home)/archive/m-([\d]{2})/d-([\d]{2})/y-([\d]{4})\/?$					{%PECIO_PATH%}index.php?target=$1&month=$2&day=$3&year=$4 [L]
RewriteRule ^(blog|home)/archive/m-([\d]{2})/y-([\d]{4})/d-([\d]{2})\/?$					{%PECIO_PATH%}index.php?target=$1&month=$2&year=$3&day=$4 [L]
RewriteRule ^(blog|home)/archive/y-([\d]{4})/d-([\d]{2})/m-([\d]{2})\/?$					{%PECIO_PATH%}index.php?target=$1&year=$2&day=$3&month=$4 [L]
RewriteRule ^(blog|home)/archive/y-([\d]{4})/m-([\d]{2})/d-([\d]{2})\/?$					{%PECIO_PATH%}index.php?target=$1&year=$2&month=$3&day=$4 [L]

RewriteRule ^(blog|home)/archive/d-([\d]{2})/m-([\d]{2})\/?$								{%PECIO_PATH%}index.php?target=$1&day=$2&month=$3 [L]
RewriteRule ^(blog|home)/archive/m-([\d]{2})/d-([\d]{2})\/?$								{%PECIO_PATH%}index.php?target=$1&month=$2&day=$3 [L]
RewriteRule ^(blog|home)/archive/m-([\d]{2})/y-([\d]{4})\/?$								{%PECIO_PATH%}index.php?target=$1&month=$2&year=$3 [L]
RewriteRule ^(blog|home)/archive/y-([\d]{4})/m-([\d]{2})\/?$								{%PECIO_PATH%}index.php?target=$1&year=$2&month=$3 [L]
RewriteRule ^(blog|home)/archive/d-([\d]{2})/y-([\d]{4})\/?$								{%PECIO_PATH%}index.php?target=$1&day=$2&year=$3 [L]
RewriteRule ^(blog|home)/archive/y-([\d]{4})/d-([\d]{2})\/?$								{%PECIO_PATH%}index.php?target=$1&year=$2&day=$3 [L]

RewriteRule ^(blog|home)/archive/d-([\d]{2})\/?$											{%PECIO_PATH%}index.php?target=$1&day=$2 [L]
RewriteRule ^(blog|home)/archive/m-([\d]{2})\/?$											{%PECIO_PATH%}index.php?target=$1&month=$2 [L]
RewriteRule ^(blog|home)/archive/y-([\d]{4})\/?$											{%PECIO_PATH%}index.php?target=$1&year=$2 [L]
';

?>