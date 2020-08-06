# Messaging App

This repo is a simple messaging api utilizing the Lumen framework.

## Description
This API is comprised of two main data entities: `Users` and `Messages`.

### User
A User is comprised of a unique, generated `id`, a unique `username`, and a hashed `password`. Users will use their ID as well as the ID of their target to send messages.

### Message
A Message is also comprised of a unique, generated `id`, message `text`, a `date` timestamp, and the IDs of the users. In this implementation, messages can be created, destroyed, but not edited.

### Conversation Endpoint
While there is no model for conversations, users can view messages sent by both themselves and their target using `GET` with the endpoint 

```bash
/{user_id1}/conversations/{user_id2}
```

As this endpoint is only for retrieving and not sending messages, users can be input into this endpoint in either order and get the same result.

Alternatively, a given user can use this endpoint to interact with their messages.
```bash
/{user_id}/messages
```

Using this endpoint with `GET` will get the user's messages, and calling it with `POST` and correct body will send a message from the given user to (to the recipient `user_id` specified in the body).

## Dependencies

[Composer](https://getcomposer.org/download/)

[Lumen framework](https://lumen.laravel.com/docs/5.1)

## Installation
After cloning the repo, initialize a SQLite database called `database.sqlite` in the `database` folder.

Then ensure dependencies are updated with Composer by running
```bash
composer update
```

## Usage

When running the app, ensure that the specific docroot is `public`, e.g., 

```bash
php -S localhost:8000 -t public
```

## Roadmap
In future iterations, OAuth2 middleware and the streamlining of the JSON Schema Valdiation systems  could be implemented.



