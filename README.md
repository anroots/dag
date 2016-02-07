# Organization Relationships

This project models a database of organization relationships as a directed graph.

Interaction with the database is done over a HTTP JSON API.

Below is an example of the type of graph modelled by this project. Each node represents a company.

![Example Graph](doc/graph.png)

## Requirements

The project requires PHP >= 7.0 with PDO, MySQL, OpenSSL and mbstring extensions. A MySQL database is used as the back-end storage.
Docker and Docker Compose runtime environment is assumed althoug not required.

## Install

### Production

Use the provided Docker image: [anroots/dag](https://hub.docker.com/r/anroots/dag).
Configure your deployment environment to run the image. Refer to [docker-compose.yml](docker-compose.yml) for configuration details.

Assuming a simple VPS with Docker and Docker Compose installed:

* Copy the file `docker-compose.yml` to the machine
* Run `MYSQL_PASSWORD=<secret> MYSQL_ROOT_PASSWORD=<secret2> APP_KEY=<secret3> docker-compose up -d`

### Development

* Clone the repository
* Copy `.env.example` into `.env` and modify the contents if necessary
* Run `composer update`
* Run `docker-compose up` to start the web and database servers
* Run `docker ps` to find the web server port
* Interact with the server via HTTP (see documentation below)

## Testing

``` bash
$ vendor/bin/phpunit
```

## API Documentation

The API communicates only over JSON: Specify `Content-Type` and `Accept` headers as `application/json` when sending requests.

Output of the API is paginated. No information is shown about the next page in the response body. Page number can be specified with the `page` query argument.

### GET `/`

Front page, no parameters, HTML output.

### GET `/organization`

Show all companies that relate to the specified company.

`parent`, `sister` and `daughter` relationships are shown.

#### Query Parameters

* `name` (string|required) The name of the vertex relative to which relationships are shown.

#### Sample Response

```json
[
    {
        "org_name": "Banana Tree",
        "relationship_type": "parent"
    },
    {
        "org_name": "Brown Banana",
        "relationship_type": "sister"
    },
    {
        "org_name": "Phoneutria Spider",
        "relationship_type": "daughter"
    }
]
```

### DELETE `/organizations`

Delete all organizations and relationships (truncate the database).

#### Query Parameters

*None*

#### Sample Response

*(empty)*

### POST `/organization`

Insert new organizations and relations to the database.

### Query Parameters

*None*

### Sample Response

*(empty)*

### Sample Request Body

```json
[
  {
    "org_name":"Paradise Island",
    "daughters":[
      {
        "org_name:":"Banana tree",
        "daughters":[
          {
            "org_name":"Yellow Banana"
          }
        ]
      },
      {
        "org_name":"Phoneutria Spider"
      }
    ]
  }
]
```