# REST Application

## About
The following is a REST based application developed using php & mysql (datasource). The Model View Controller Architecture pattern has been followed in the development of this application. The application supports auto installation and uses no framework absolutely. PSR4 is used for class autoloading.

## Setup Instructions
1. Clone the git files.
2. Fetch the required files (autoload files) using composer.
	composer install
3. To access the application setup a virtual host to this directory location
	'app/public';
4. Open the virtual host URL in your browser to configure database details and complete the installation.

[Note] Make sure you have a MYSQL user & database ready before installation.


## Using Application

The entire application is bootstraped. All request must come from 'public/index.php' which then routes the request to the required controller & method.

### Calling a controller
Pass the controller id in the argument with key as 'c'
eg. index.php?c=product

If you define a controller class as ProductController, its id is 'product'.

If no controller is provided, system is configured to use 'product' as default controller.

### Calling a controller method
Pass the method id in the argument with key as 'm'
eg. index.php?c=product&m=update

If you define a method as indexMethod, its id is 'index'. To make a method accessible, 'Method' must be appended to the method name.

If no method is provided, system is configured to use 'index' as default method.

### Restricting access to an API
Any method can be made private (accessible to users only). In the controller define $accessRule and map the methodID to '@'.
```
public $accessRule = [
        'index' => '@',
        'update' => '@',
        'delete' => '@',
        'create' => '@',
    ];
```


### Defining request types to an API
From the controller itself, the request type accepted by the method can be configured. verbs() function must return the request type accepted by each method.
```
public function verbs()
    {
        return (array_merge( parent::verbs(), ['search' => ['GET']]));
    }
```

### Accessing a private API
To access a private API user must provide an 'access_key' as GET argument.
```
&access_key='your access key'
```

### Generating access key
To generate an access key user must login with valid credentials. Access Key is randomly generated on each request.
```
c=user
m=login
username='your_username'
password='your_password'
http://automitra.com/rest/restApp/app/public/index.php?c=user&m=login&username=admin&password=admin
```


### Default API Configuration
```
Controller: product

index - Lists all products (Page wise (Accepts page argument)) - GET - Member Access only
update - Updates a product - PATCH - Member Access only
create - Creates a new product - POST - Member Access only
delete - Delete a product - DELECT - Member Access Only
search	- Accepts 'name' argument - GET - Public

http://automitra.com/rest/restApp/app/public/index.php?c=product&m=search&name=ro
```


## Different APIs

### List All Products
```
Controller: product
Method: index
Access: Private
Request Type: GET
GET Parameter: 'access_key', 'page'

This API returns list of all products each page at a time. 10 results are by default configured to be fetched per page.

Sample:
http://automitra.com/rest/restApp/app/public/index.php?c=product&access_key=XuP7jLJL4ykZqgZO63ndh6bWOwWqNN1K
```

### Create a new product
```
Controller: product
Method: create
Access: Private
Request Type: POST
GET Parameter: 'access_key'

This API will add a new product. 
(id, name, description and cost are the parameters for each product).

Sample POST:
http://automitra.com/rest/restApp/app/public/index.php?c=product&m=create&access_key=XuP7jLJL4ykZqgZO63ndh6bWOwWqNN1K
[The values must be provided in the request body]
```

### Updating a product
```
Controller: product
Method: update
Access: Private
Request Type: PATCH
GET Parameter: 'access_key', 'id'

This API will update a product details. The product to be updated is identified by its id.
(id, name, description and cost are the parameters for each product).

Sample PATCH:
http://automitra.com/rest/restApp/app/public/index.php?c=product&access_key=XuP7jLJL4ykZqgZO63ndh6bWOwWqNN1K&m=update&id=3
[The values must be provided in the request body]
```

### Delete a product
```
Controller: product
Method: delete
Access: Private
Request Type: DELETE
GET Parameter: 'access_key'


Sample DELETE:
http://automitra.com/rest/restApp/app/public/index.php?c=product&access_key=XuP7jLJL4ykZqgZO63ndh6bWOwWqNN1K&m=delete

[id] must be provided in the body.
```

### Search a product
```
Controller: product
Method: search
Access: Public
Request Type: GET
GET Parameter: 'name'


Sample GET:
http://automitra.com/rest/restApp/app/public/index.php?c=product&m=search&name=ro

Search is only based on the 'name' parameter.
```
