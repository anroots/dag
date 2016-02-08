# Organization Relationships

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Total Downloads][ico-downloads]][link-downloads]

This project models a database of organization relationships as a directed graph. Interaction with the database is done over a HTTP JSON API.

Below is an example of the type of graph modelled by this project. Each node represents a company.

![Example Graph](doc/graph.png)

*N.B.* Terminology: organization, company and vertex are used interchangeably.

## Requirements

The project requires PHP >= 7.0 with PDO, MySQL, OpenSSL and mbstring extensions. A MySQL database is used as the back-end storage.
Docker and Docker Compose runtime environment is assumed although not required.

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

### Migrating the Database

The initial setup involves database migration and optionally, seeding.

Assuming that the project runs in a Docker container...

* Go into the container: `docker exec -it dag_app_1 bash`
* Migrate the database: `./artisan migrate`
* Seed the database: `./artisan db:seed`

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
* `page` (int|optional) Current page number

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

## Performance Considerations

The current implementation of the project is meant to handle small to medium usage. ~>100K relations will be slow if not unusable.

A better (read: optimized for high data set / high load applications) solution would involve a better back-end data model (perhaps something that can optimize for "sister" relationships, as [detailed here](http://www.codeproject.com/Articles/22824/A-Model-to-Represent-Directed-Acyclic-Graphs-DAG-o)) and additional techniques such as caching, queueing and load-balancing.
## License

MIT license

[ico-version]: https://img.shields.io/packagist/v/anroots/dag.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/anroots/dag/master.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/anroots/dag.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/anroots/dag
[link-travis]: https://travis-ci.org/anroots/dag
[link-downloads]: https://packagist.org/packages/anroots/dag
