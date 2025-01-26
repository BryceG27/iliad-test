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
From now on you can start using all the APIs written in the <strong style="font-size: 16px">The APIs</strong> paragraph, but you could make just another step that could make your testing just a little easier seeding the database:

In the shell of your Docker container or your local directory you can:
<ul>
    <li>
        Create a test user using the command <code>php artisan db:seed</code>;
    </li>
    <strong>OR</strong>
    <li>Create a test user and some test products using the command <code>php artisan db:seed DevSeeder</code></li>
</ul>

Both commands will create a new user with <em>test@example.com</em> as email and <em>supersecurepassword</em> as password. <br>
The <em>DevSeeder</em> option will create some products to help you start create some orders.

## The APIs
In the document root you can find a postman collection of APIs named <strong>Tech-Test.postman_collection.json</strong> that is ready to be imported. 

### Before start
<ul>
    <li>
        You need to be logged to execute the 95% of the API calls.
    </li>
    <li>
        The URL is always formatted as: <strong>{{ Hostname }}/api/{{ api-version }}/{{ endpoint }}</strong>.
        <ul>
            <li>Example: <em>localhost:8000/api/v1/products</em></li>
            <li>Only the APIs to handle the data of the User resource don't have the <strong>{{ api-version }}</strong> parameter</li>
        </ul>
    </li>
    <li>
        Every response is in the following format: <br>
        <code>
        { <br>
            &nbsp;&nbsp;"success" : Boolean // If there is something wrong in your request, this flag will be false, otherwise it'll be true <br>
            &nbsp;&nbsp;"error_message" : null/String // A short description of the error<br>
            &nbsp;&nbsp;"errors" : null/Array // A list of all the errors found with your request<br>
            &nbsp;&nbsp;"data" : { <br>
            &nbsp;&nbsp; "message" : "Updated" <br>
            &nbsp;&nbsp;// This section contains a message response in case the request was successful. <br>
            &nbsp;&nbsp;// In case you request or update a resource, the data will also be displayed here <br>
            &nbsp;&nbsp;}<br>
        }
    </code>
    </li>
</ul>

### Dipping down

<ul>
    <li>
        To loggin in you have to reach the endpoint <code>/login</code> with the credentials gave to you in the <strong style="font-size: 16px">What now</strong> paragraph. <strong>N.B. From now on all the future calls and endpoints described in this paragraph will be accessible only if authenticated!</strong>;
        <ul>
        <li>
            This call will give to you a <em>Bearer token</em> to use to execute all of your future calls as authenticated user;
        </li>
            <li>
                In case you have not seeded your database, you have to create a new user using the HTTP POST API at the endpoint <code>/user</code> and insert an email, a name and a password as JSON body of this call.
            </li>
        </ul>
    </li>
</ul>

#### Products

<ul>
    <li>
        To access all products you have to reach the endpoint <code>/v1/products</code> using HTTP GET as HTTP method. You can insert two params in the query to filter by name or to show items that are unavailable as query params:
        <ul>
            <li>
                The param <code>?name={{ name }}</code> can help you to filter all the products containing the string you inserted;
            </li>
            <li>
                A product is not available if the calculated <em>quantity</em> (quantity in stock - quantity requested) is 0 and by default this products are not shown, but you can request to show unavailable products too using the param <code>?show_not_available=</code> setted to 1;
            </li>
        </ul>
    </li>
    <li>
        To access a single product the endpoint is <code>/v1/products/{{ product_id }}</code> using HTTP GET as HTTP method where product_id is, ofcourse, the id of the product requested;
    </li>
    <li>
        To create some new products you can reach the endpoint <code>/v1/products</code> using HTTP POST as HTTP method.
        <ul>
            <li>
                The only parameter required here is the {{ name }}. The orders can be updated in a second moment;
            </li>
            <li>
                The other parameters are the float {{ price }} and the integer {{ quantity }};
            </li>
        </ul>
    </li>
    <li>
        You can update a product reaching the endpoint <code>/v1/products/{{ product_id }}</code> using HTTP PATCH as HTTP method where product_id is the id of the product to update
        <ul>
            <li>The parameters are <code>name</code>, <code>price</code>, <code>quantity</code>;</li>
        </ul>
    </li>
    <li>
        You can delete a product reaching the endpoint <code>/v1/products/{{ product_id }}</code> using HTTP DELETE as HTTP method where product_id is the id of the product to delete
    </li>
</ul>


#### Orders

<ul>
    <li>
        To access all orders you have to reach the endpoint <code>/v1/orders</code> using HTTP GET as HTTP method. You can insert two params in the query to filter by name, description, date or to show orders deleted as query params:
        <ul>
            <li>
                The param <code>?name={{ name }}</code> can help you to filter all the orders containing the string you inserted;
            </li>
            <li>
                The param <code>?description={{ description }}</code> can help you to filter all the orders containing the string you inserted;
            </li>
            <li>
                The param <code>?date={{ date }}</code> can help you to filter all the orders created with the date you inserted;
            </li>
            <li>
                By default, only the orders with status "Pending" or "Completed" are shown. You can set the parameter <code>?show_deleted={{ 1 }}</code> to show deleted orders too.
            </li>
        </ul>
    </li>
    <li>
        To access a single order the endpoint is <code>/v1/orders/{{ order_id }}</code> using HTTP GET as HTTP method where order_id is, ofcourse, the id of the order requested;
    </li>
    <li>
        To create some new orders you can reach the endpoint <code>/v1/orders</code> using HTTP POST as HTTP method.
        <ul>
            <li>
            The required parameters are <code>name</code>, <code>description</code>, <code>date</code> and <code>products</code>;
            </li>
            <li>
                The parameter products is an array of object and every single object must contain the id of the desired product and the requested quantity;
                <ul>
                    <li>
                        If the requested quantity is greater than the available quantity, the response will be false;
                    </li>
                    <li>
                        If one of the products is not found, the response will be false;
                    </li>
                </ul>
            </li>
            <li>
                The optional parameter type is named <code>type</code> and indicates if the order is to a customer (0) or to a supplier (1).<br>
                In the first case, the quantity of the ordered products will be handled as "busy".
            </li>
        </ul>
    </li>
    <li>
        You can update a order reaching the endpoint <code>/v1/orders/{{ order_id }}</code> using HTTP PATCH as HTTP method where order_id is the id of the order to update, but only if the status of the order is still "Pending" (0);
        <ul>
            <li>
                The parameters are the same as the store call;
            </li>
        </ul>
    </li>
    <li>
        You can confirm an order reaching the endpoint <code>/v1/orders/{{ order_id }}/confirm</code> using HTTP PUT as HTTP method where order_id is the id of the order to confirm.
        <ul>
            <li>If the type of the order is "To Customer", the confirmation of the order will reduce the stock of the products;</li>
            <li>If the type of the order is "From Supplier", the confirmation of the order will increse the stock of the products.</li>
        </ul>
    </li>
    <li>
        You can delete a order reaching the endpoint <code>/v1/orders/{{ order_id }}</code> using HTTP DELETE as HTTP method where order_id is the id of the order to delete
        <ul>
            <li>
                If the type of the order is "To Customer" and the order was already confirmed, the confirmation of the order will increse the stock of the products;
            </li>
            <li>
                If the type of the order is "From Supplier" and the order was already confirmed, the confirmation of the order will reduce the stock of the products.
            </li>
        </ul>
    </li>
</ul>

## Testing

#### The testing environment is setted to be use out of the box with Docker. In case you are not using Docker, follow this steps:

- You can change <code>DB_CONNECTION</code> parameter to sqlite and comment all the rest of <code>DB_*</code> parameters to have a simple to use database.

#### OR

<ol>
    <li>
        Change the parameter <code>DB_PORT</code> from db_test to 127.0.0.1;
    </li>
    <li>
        Create a new database, <code>laravel_test_db</code> will do the job;
    </li>
    <li>
        Change the parameters <code>DB_USERNAME</code> and <code>DB_PASSWORD</code> as requested.
    </li>
</ol>

### The test framework
The test framework used for this project is PEST. It's been created by one of the Laravel Developers so the integration is flawless and the syntax of it is really simple.

The following tests are all written in the directory <code>tests/Feature</code>:
<ul>
    <li>
        <strong>\Auth\AuthenticationTest</strong> will:
        <ol>
            <li>
                Check if users can authenticate;
            </li>
            <li>
                Check if users cannot authenticate with wrong password;
            </li>
            <li>
                Check if users can logout;
            </li>
            <li>
                Check if users can operate if they are not logged.
            </li>
        </ol>
    </li>
    <li>
        <strong>\Auth\RegistrationTest</strong> will:
        <ol>
            <li>
                Check if users can register new users;
            </li>
            <li>
                Check if users can create a new user with API.
            </li>
        </ol>
    </li>
    <li>
        <strong>\Auth\UpdateTest</strong> will update user data with API.
    </li>
    <li>
        <strong>CRUDProductTest</strong> will:
        <ol>
            <li>
                Check if users can create a product;
            </li>
            <li>
                Check if users can update a product;
            </li>
            <li>
                Check if users can delete a product.
            </li>
        </ol>
    </li>
    <li>
        <strong>CRUDOrderTest</strong> will:
        <ol>
            <li>
                Check if users can create a order;
            </li>
            <li>
                Check if users can update a order;
            </li>
            <li>
                Check if users can delete a order.
            </li>
        </ol>
    </li>
</ul>

To start all the tests you have to use the command `php artisan test` from the shell of your Docker container or of your local repository.
