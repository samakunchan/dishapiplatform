# Dish API Platform

#### Description
This an API created with the API Platform template.

#### Prerequisite knowledge

- Docker: No level needed or rookie level. Just follow commands line. You must have docker in your machine.
- Symfony: Require. Because you have to setup database and JWT.
- API Platform: Require. You still have the documentation if you forgot something: https://api-platform.com/docs

If you also want to use this template, check this link: https://github.com/api-platform/api-platform

## Installation
This installation has two parts: installing the project and installing the database

#### The project
- Clone the repository:

      git clone https://github.com/samakunchan/dishapiplatform.git

- Pull images
        
      docker-compose pull

- Start all services

      docker-compose up -d
      # it can be a bit long

- Install dependencies

      docker exec -it dishapiplatform_php_1 sh
      # if you have window add 'winpty' key word like below
      winpty docker exec -it dishapiplatform_php_1 sh

When you run this command line, you go inside the PHP's container and you are able to use `composer`.

      composer install
        
#### The database
Stay inside the PHP's container and run this command below...
     
     touch .env.local
     # it will create a specific file
    
... and paste this line below.

     DATABASE_URL=postgres://api-platform:secret@db/api?server_version=12

The `DATABASE_URL` in `.env.local` will overwrite safely the `DATABASE_URL` in `.env`.

NB: Create `.env.local` is up to you. You can do everything in `.env` file.

## Initialize datas
Stay inside the PHP's container and run this command below...

     php bin/console d:d:c      # it create the database
     php bin/console d:s:u -f   # it update schema
     php bin/console d:fi:l     # it load fixture (optional)
     
And the API is ready to use with: https://localhost:8443/api
     
