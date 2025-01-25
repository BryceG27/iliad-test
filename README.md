# Technical Test - How to fire it up

## Setting-up development environment

- The first step is to create a copy of the dotfile `.env.example` and rename it as `.env`. A simple `cp .env.example .env` should do the job.

### With Docker (suggested)

#### Things to know before starting with Docker
The <code>.env</code> file is setted to map ports for a multi-environment system: change <code>*_PORT_EXPOSED</code> to your port mapping preferences

#### Commands list

<ul>
    <li>
        Using docker, the only thing to do is insert the command <code>make</code> in the shell;      
    </li>
    <li>
        This command will: 
        <ol>
            <li>
                install all the Laravel dependences;
            </li>
            <li>
                generate a key for your Laravel application;
            </li>
            <li>
                start all the containers needed;
            </li>
            <li>
                enter in the shell of the container named <strong><em>app</em></strong>;
            </li>
        </ol>
    </li>
    <li>
        In the shell of the app container you just need to insert the command <code>php artisan migrate</code> to setup your database
    </li>
</ul>

#### Makefile error
In case the Makefile and the make command won't work <em>(or you don't want to use it)</em> you you'll have to do the steps one by one by yourself:
<ol>
    <li>
        Install all the Laravel dependences with the command <code>docker compose run --rm composer install</code>;
    </li>
    <li>
        Start up all the containers with the command <code>docker compose up -d</code>;
    </li>
    <li>
        Enter in the shell of the app container (<code>docker exec -it app sh</code> will work just fine);
    </li>
    <li>
        Generate the key for Laravel using the command <code>php artisan key:generate</code>
    </li>
    <li>
        Only then you can setup the database with the command <code>php artisan migrate</code>;
    </li>
</ol>

### With the traditional way

#### Things to know before starting w/o Docker
 - The database parameters are setted to use Docker containers and variables. You'll need to change the variable <code>DB_HOST</code> to <em>localhost</em> or to <em>127.0.0.1</em> instead of <em>DB</em>. Of course you'll have to change <code>DB_DATABASE</code>, <code>DB_USERNAME</code> and <code>DB_PASSWORD</code> variables too.
 - For this version of Laravel (v.11), sqlite is the default choice: you need just to comment all <code>DB_*</code> variables except the CONNECTION one that needs to be <code>sqlite</code>.
 - This version of Laravel (v.11) needs at least the version 8.2 of PHP.

#### Commands list
<ol>
    <li>
        The first thing to do is install all the Laravel dependences with the command <code>compose install</code>;
    </li>
    <li>
        Use the command <code>php artisan key:generate</code> to generate the unique key for your app;
    </li>
    <li>
        Now you just need to insert the command <code>php artisan migrate</code> to setup your database.
    </li>
</ol>

## What now
At the moment your app should be ready to use and you can verify it looking for the home page (<a href="localhost:8000" _target="blank">localhost:8000</a>) where you will find the version of the installed Laravel.<br>
From now on you starting to use all the API written in the <strong style="font-size: 16px">The APIs</strong> paragraph, but you could make just another step that could make your testing just a little easier seeding the database:

In the shell of your Docker container or your local directory you can:
<ul>
    <li>
        Create a test user using the command <code>php artisan db:seed</code>;
    </li>
    <strong>OR</strong>
    <li>Create a test user and some test products using the command <code>php artisan db:seed DevSeeder</code></li>
</ul>

## The APIs
