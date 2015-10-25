[![Build Status](https://travis-ci.org/andela-doladosu/ngemoji.svg?branch=master)](https://travis-ci.org/andela-doladosu/ngemoji)

#NGemoji
NGemoji is a lightweight API for naija emojis built using the `Slim` framework.
It allows basic API calls and performs CRUD operations as requested by the end user.

#Testing
 phpspec for testing is used to perform 
 tests on the classes. The TDD principle has been
 employed to make the application robust

 Run this in your terminal to execute the tests
 ```````
 /vendor/bin/phpspec run
`````````

#Install

- To install this package, PHP 5.5+ and Composer are required

``````
composer require dara/ngemoji
``````
#Environment variables

In order to use this package, you need to create a `.env` file in 
your project root folder with the following details provided
`````
P_DRIVER = 'mysql';
P_HOST   = 'localhost';
P_DBNAME = 'db_name';
P_USER   = 'db_username';
P_PASS   = 'db_password';
`````
#usage

- Register a new user

Call the `/auth/register` API endpoint with the following options
````````
method   = POST

form parameters

username = anyusername 
password = anypassword
`````````

- Login user

Call the `/auth/login` API endpoint with the following options
````````
method = POST

header parameters

token = generatedToken
`````````

- Logout user

Call the `/auth/logout` API endpoint with the following options
````````
method = GET

header parameters

token = generatedToken
`````````

- Add an emoji

Call the `/emojis` API endpoint with the following options
````````
method = POST

form parameters

name       = anyEmojiName
category   = anyCategory
char       = anyEmojiChaaracter
keywords[] = keyword1
keywords   = keyword2

header parameters

token = generatedToken
`````````

- Update an emoji

Call the `/emojis/:id` API endpoint with the following options
````````
method = PUT/PATCH

form parameters

name       =  anyEmojiName
category   =  anyCategory
char       =  anyEmojiChaaracter
keywords[] =  keyword1
keywords[] =  keyword2

header parameters

token = generatedToken
`````````

- Delete an emoji

Call the `/emojis/:id` API endpoint with the following options
````````
method = DELETE

header parameters

token = generatedToken
`````````


## Change log
Please check out [CHANGELOG](CHANGELOG.md) file for information on what has changed recently.

## Contributing
Please check out [CONTRIBUTING](CONTRIBUTING.md) file for detailed contribution guidelines.

## Credits
NGemoji is maintained by `Dara Oladosu`.

## License
NGemoji is released under the MIT Licence. See the bundled [LICENSE](LICENSE.md) file for more details.


