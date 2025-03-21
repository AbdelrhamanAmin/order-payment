# Order-Payment

### Technologies

| Type    | Name / Version |
| ------- | -------------- |
| php     | 8.4.3          |
| laravel | 12             |
| docker  | custom docker  |
| queue   | redis          |
| db      | mysql/8.0      |

### Prerequisites

- install [docker](https://docs.docker.com/engine/install/)
- start docker

<br>

## Setup

> ### For local installation
>
>- clone the app to your machine
>
>    ```shell
>    git clone  https://github.com/AbdelrhamanAmin/order-payment.git
>    ```
>
>- ```shell
>    cd order-payment
>   ```
>
>- duplicate `.env.example` to `.env`
>
>    ```shell
>    cp .env.example .env
>    ```
>
>- Run `make up` to get the dev environment booted
>- visit <http://localhost>
>

<br>

## Usage

| Command              | Meaning                                  |
| -------------------- | ---------------------------------------- |
| `make help`          | list all make commands                   |
| `make up`            | build docker images                      |
| `make rebuild`       | rebuilds the Docker images               |
| `make down`          | stop docker with remove images           |
| `make migrate`       | Run migrations & run seeders             |
| `make horizon`       | Start Laravel Horizon                    |



<br>

### Queue Management with Laravel Horizon
This project uses Laravel Horizon to manage queues efficiently.

> Start Laravel Horizon with the following command:
>   ```sh
>    make horizon
>   ```
>  visit <http://localhost/horizon> to Queues dashboard

### Orders Processing
To dispatch orders to the queue, you can run:

>   ```sh
>    ./vendor/bin/sail artisan order:process-orders
>   ```

This is an artisan command that process pending orders in database to the queuing system.
