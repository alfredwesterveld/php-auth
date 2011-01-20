Note
--
I don't even think you should be using this
class(you should not store passwords), but use federated login like for example
OpenID. I would  like to point out to read this very interesting blog post
["The Dirty Truth About Web Passwords " by Jeff Atwood](http://www.codinghorror.com/blog/2010/12/the-dirty-truth-about-web-passwords.html)
Luckily I am smart enough NOT to store the passwords in plain-text,
but use the very good
[phpass library](http://www.openwall.com/phpass/)
to store passwords safely in the database.
I just don't think you should create yet another account to use a service.
I would also like to point out that if you don't use SSL your password would be
sent over the wire in plaintext which I think is also pretty BAD.
the (good) OpenID providers have SSL in place.
I think you should use the very good [LightOpenID](http://gitorious.org/lightopenid)
library to use OpenID.

Requirements
--

- PHP > 5.1.0
  I think you should have at least PHP 5.2.0 because only from that version the 
  filter extension is enabled by default. although this code does not output any
  unsafe code, so XSS is not possible.
- PDO(PHP > 5.1.0 has PDO enabled by default).

Getting started
--

Added a basic example in `example` folder.

Database
--

You should update `$db = new PDO('sqlite:database/login.sqlite3');`  inside
`database.php` to use your database.

The database needs the following schema

    CREATE TABLE IF NOT EXISTS users
    (id INTEGER PRIMARY KEY,
    username TEXT NOT NULL UNIQUE,
    email TEXT NOT NULL,
    hash TEXT NOT NULL,
    active BOOLEAN)

There is also a `createDatabase.php` which tries to create an SQLite3 database.
First you should ensure that PHP(Apache) has permission to access your
database if you are using this file because it uses the filesystem.
I am using this SQLite3 database in my example.

Todo
--

- I would like for email to be unique, but right now that breaks some tests and
 I do not have any time right now to finish this.
- The ability to protect creating(and or login) of accounts with CAPTCHA.
- The ability to protect account creation by sending of emails. There is code in
 place to do this(Authentication.php), but not yet in the example.