# Hyde - Convert yaml to sql data

Hyde will take a folder full of markdown documents and convert them to an SQL table.  The yaml frontmatter is used to populate a number of the fields.  The 
The table fields are as follows:

```sql
id INTEGER NOT NULL PRIMARY KEY, -- (auto generated)
file VARCHAR(255), -- (the name of the file that was parsed)
meta TEXT, markdown of the document is converted to HTML and saved also.
-- (json encoded array of the yaml front matter)
html TEXT, -- (markdown (without yaml) converted to HTML)
created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, -- (file timestamp)
status TINYINT NOT NULL DEFAULT 0 -- (public or private)
```

## Installation

Install with composer:

```bash
composer require pagerange/hyde
```

Copy the `hyde` binary (php shell script) to your project root (`vendors` should be in same folder as `hyde` binary):

```bash
cp vendor/pagerange/hyde/bin/hyde .
```

Add config options to your .env file:

```
DB_CONNECTION=
DB_DATABASE=
HYDE_DOCPATH=
HYDE_DOCTABLE=
```

Example using sqlite:

```
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
HYDE_DOCPATH=markdown/docs
HYDE_DOCTABLE=docs
```

Example using mysql:

```
DB_CONNECTION=mysql
DB_DATABASE=my_db_name
DB_USERNAME=web_user
DB_PASSWORD=mypass
HYDE_DOCPATH=markdown/docs
HYDE_DOCTABLE=docs
```

`HYDE_DOCPATH` is required, or an exception will be thrown

`HYDE_DOCTABLE` is optional, and will default to `hyde_docs`

## Sample markdown

```markdown
---
title: Test Document Number One
author: Joe Cool
description: The first document in the Hyde test suite
tags: 
    - test
    - phpunit
    - php
status: public
created_at: 2019-12-07 04:30:00
---
# Test Document Number One

Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
proident, sunt in culpa qui officia deserunt mollit anim id est laborum.

* bullet one
* bullet two
* bullet three

```

## Contribute

There's not much too this yet.  Just a utility I developed for a personal project.  However, if you feel like working on it, please fork and make pull requests for whatever enahancements you make.

Thanks!
